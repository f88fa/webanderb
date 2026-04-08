<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SectionOrder extends Model
{
    protected $table = 'section_order';

    protected $fillable = [
        'section_key',
        'section_name',
        'order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'order' => 'integer',
    ];

    /** مفتاح القسم الواحد القديم (أقسام البانر مجمّعة) — نستبدله ببانر فردي لكل عنصر */
    const LEGACY_BANNER_SECTIONS_KEY = 'banner_sections';

    /**
     * Default sections (without banner_sections — كل بانر يُضاف لوحده عبر syncBannerSections)
     */
    public static function getDefaultSections(): array
    {
        return [
            ['section_key' => 'about', 'section_name' => 'من نحن', 'order' => 1, 'is_visible' => true],
            ['section_key' => 'vision_mission', 'section_name' => 'الرؤية والرسالة', 'order' => 2, 'is_visible' => true],
            ['section_key' => 'services', 'section_name' => 'الخدمات', 'order' => 3, 'is_visible' => true],
            ['section_key' => 'projects', 'section_name' => 'المشاريع', 'order' => 4, 'is_visible' => true],
            ['section_key' => 'media', 'section_name' => 'المركز الإعلامي', 'order' => 5, 'is_visible' => true],
            ['section_key' => 'testimonials', 'section_name' => 'ماذا قالوا عنا', 'order' => 6, 'is_visible' => true],
            ['section_key' => 'partners', 'section_name' => 'الشركاء', 'order' => 7, 'is_visible' => true],
            ['section_key' => 'news', 'section_name' => 'الأخبار', 'order' => 8, 'is_visible' => true],
        ];
    }

    /**
     * Seed default sections if table is empty
     */
    public static function seedDefaultsIfEmpty(): void
    {
        if (self::count() > 0) {
            return;
        }
        foreach (self::getDefaultSections() as $row) {
            self::create($row);
        }
    }

    /**
     * استبدال القسم المجمع "أقسام البانر" بصف واحد لكل بانر، ومزامنة البانرات الحالية
     */
    public static function syncBannerSections(): void
    {
        if (self::where('section_key', self::LEGACY_BANNER_SECTIONS_KEY)->exists()) {
            self::where('section_key', self::LEGACY_BANNER_SECTIONS_KEY)->delete();
        }

        $maxOrder = (int) self::max('order');
        $banners = BannerSection::orderBy('order')->get();

        foreach ($banners as $banner) {
            $key = 'banner_section_' . $banner->id;
            if (self::where('section_key', $key)->exists()) {
                continue;
            }
            $maxOrder++;
            self::create([
                'section_key' => $key,
                'section_name' => self::bannerSectionName($banner),
                'order' => $maxOrder,
                'is_visible' => true,
            ]);
        }
    }

    /**
     * اسم عرض لبانر في قائمة ترتيب الأقسام
     */
    public static function bannerSectionName(BannerSection $banner): string
    {
        $title = $banner->title ? trim($banner->title) : null;
        if ($title !== null && $title !== '') {
            return 'بانر: ' . $title;
        }
        if ($banner->youtube_video_id || $banner->video) {
            return 'بانر: فيديو';
        }
        if ($banner->image) {
            return 'بانر: صورة';
        }
        return 'بانر: ' . $banner->id;
    }

    /**
     * إضافة صف ترتيب لبانر جديد (يُستدعى من BannerSection عند الإنشاء)
     */
    public static function addBannerSection(int $bannerId, string $sectionName): void
    {
        $key = 'banner_section_' . $bannerId;
        if (self::where('section_key', $key)->exists()) {
            return;
        }
        $maxOrder = (int) self::max('order');
        self::create([
            'section_key' => $key,
            'section_name' => $sectionName,
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);
    }

    /**
     * حذف صف ترتيب البانر (يُستدعى من BannerSection عند الحذف)
     */
    public static function removeBannerSection(int $bannerId): void
    {
        self::where('section_key', 'banner_section_' . $bannerId)->delete();
    }

    /**
     * Get all sections ordered (for admin); ensures defaults and banner rows exist
     */
    public static function getAllOrdered()
    {
        self::seedDefaultsIfEmpty();
        self::syncBannerSections();
        $rows = self::orderBy('order')->get();
        self::hydrateBannerNames($rows);
        return $rows;
    }

    /**
     * تحديث section_name لصفوف البانر من جدول البانرات (للعرض في لوحة الترتيب)
     */
    protected static function hydrateBannerNames($rows): void
    {
        $bannerIds = [];
        foreach ($rows as $row) {
            if (str_starts_with($row->section_key, 'banner_section_')) {
                $id = (int) str_replace('banner_section_', '', $row->section_key);
                if ($id > 0) {
                    $bannerIds[$id] = $row;
                }
            }
        }
        if (empty($bannerIds)) {
            return;
        }
        $banners = BannerSection::whereIn('id', array_keys($bannerIds))->get()->keyBy('id');
        foreach ($bannerIds as $id => $sectionRow) {
            $banner = $banners->get($id);
            if ($banner) {
                $sectionRow->section_name = self::bannerSectionName($banner);
            }
        }
    }

    /**
     * Get visible sections ordered (for frontend)
     */
    public static function getVisibleOrdered()
    {
        self::seedDefaultsIfEmpty();
        self::syncBannerSections();
        return self::where('is_visible', true)->orderBy('order')->get();
    }

    /**
     * Update section order (and create missing banner_section_X if sent from front)
     */
    public static function updateOrder(array $sections)
    {
        foreach ($sections as $index => $section) {
            $key = $section['key'] ?? null;
            if (!$key) {
                continue;
            }
            $order = $index + 1;
            $visible = $section['visible'] ?? true;

            $row = self::where('section_key', $key)->first();
            if ($row) {
                $row->update(['order' => $order, 'is_visible' => $visible]);
            } else {
                if (str_starts_with($key, 'banner_section_')) {
                    $id = (int) str_replace('banner_section_', '', $key);
                    if ($id > 0) {
                        self::create([
                            'section_key' => $key,
                            'section_name' => 'بانر: ' . $id,
                            'order' => $order,
                            'is_visible' => $visible,
                        ]);
                    }
                }
            }
        }
    }
}
