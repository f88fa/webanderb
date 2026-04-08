<?php

namespace App\Http\Controllers\Wesal;

use App\Http\Controllers\Controller;
use App\Models\AboutFeature;
use App\Models\AboutStat;
use App\Models\AboutUs;
use App\Models\BannerSection;
use App\Models\BoardMember;
use App\Models\File;
use App\Models\HeroSliderImage;
use App\Models\InternalMessage;
use App\Models\InternalMessageAttachment;
use App\Models\InternalMessageRecipient;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\MediaSlide;
use App\Models\MediaVideo;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Partner;
use App\Models\PaymentRequest;
use App\Models\Policy;
use App\Models\PolicyCategory;
use App\Models\Project;
use App\Models\Report;
use App\Models\SectionOrder;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Staff;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskUpdate;
use App\Models\TaskUpdateAttachment;
use App\Models\Testimonial;
use App\Models\VisionMission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * إعادة النظام إلى الحالة الافتراضية (للديمو).
 * المسار سري: wesal/bbaacckk — لا يظهر أي زر في الواجهة.
 * يحذف: القيود وطلبات الصرف، الرسائل الداخلية، الوسائط، البانرات، الأخبار، المحتوى المضاف، ويمسح مراجع الملفات من الإعدادات.
 */
class ResetToDefaultController extends Controller
{
    /**
     * حذف ملف من التخزين (مسار نسبي مثل uploads/...)
     */
    private static function deleteStorageFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }
        $path = str_replace('storage/', '', ltrim($path, '/'));
        if ($path === '') {
            return;
        }
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function __invoke(): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // 1) طلبات الصرف والقيود
            PaymentRequest::query()->forceDelete();
            JournalLine::query()->delete();
            JournalEntry::query()->forceDelete();

            // 2) الرسائل الداخلية (مرفقات ثم مستقبلين ثم الرسائل)
            foreach (InternalMessageAttachment::all() as $att) {
                self::deleteStorageFile($att->path);
            }
            InternalMessageAttachment::query()->delete();
            InternalMessageRecipient::query()->delete();
            InternalMessage::query()->delete();

            // 3) المهام والمهام الفرعية (المكتب الإلكتروني)
            foreach (TaskUpdateAttachment::all() as $att) {
                self::deleteStorageFile($att->path ?? $att->file_path ?? null);
            }
            TaskUpdateAttachment::query()->delete();
            TaskUpdate::query()->delete();
            TaskAssignee::query()->delete();
            Task::query()->delete();

            // 4) الوسائط (فيديوهات وسلايدات)
            foreach (MediaVideo::all() as $v) {
                self::deleteStorageFile($v->thumbnail ?? null);
            }
            MediaVideo::query()->delete();
            foreach (MediaSlide::all() as $s) {
                self::deleteStorageFile($s->image ?? null);
            }
            MediaSlide::query()->delete();

            // 5) أقسام البانر (الصور/الفيديو ثم السجلات — الحذف يزيل ترتيب الأقسام تلقائياً)
            foreach (BannerSection::all() as $b) {
                self::deleteStorageFile($b->image ?? null);
                self::deleteStorageFile($b->video ?? null);
            }
            BannerSection::query()->delete();

            // 6) صور سلايدر الهيرو
            foreach (HeroSliderImage::all() as $img) {
                self::deleteStorageFile($img->image ?? null);
            }
            HeroSliderImage::query()->delete();

            // 7) الأخبار
            foreach (News::all() as $n) {
                self::deleteStorageFile($n->image ?? null);
            }
            News::query()->delete();

            // 8) المحتوى المضاف (من نحن، رؤية ورسالة، خدمات، شركاء، مشاريع، آراء، مجلس إدارة، موظفين، تقارير، لوائح، ملفات، قائمة)
            foreach (BoardMember::all() as $m) {
                self::deleteStorageFile($m->image ?? null);
            }
            BoardMember::query()->delete();
            foreach (Staff::all() as $s) {
                self::deleteStorageFile($s->image ?? null);
            }
            Staff::query()->delete();
            foreach (Policy::all() as $p) {
                self::deleteStorageFile($p->file ?? null);
            }
            Policy::query()->delete();
            PolicyCategory::query()->delete();
            foreach (File::all() as $f) {
                self::deleteStorageFile($f->file_path ?? null);
            }
            File::query()->delete();

            AboutFeature::query()->delete();
            AboutStat::query()->delete();
            AboutUs::query()->delete();
            VisionMission::query()->delete();
            Service::query()->delete();
            Partner::query()->delete();
            Project::query()->delete();
            Testimonial::query()->delete();
            Report::query()->delete();
            MenuItem::query()->update(['parent_id' => null]);
            MenuItem::query()->delete();

            // 9) ترتيب الأقسام — إعادة إلى الافتراضي (بدون بانرات)
            SectionOrder::query()->delete();
            foreach (SectionOrder::getDefaultSections() as $row) {
                SectionOrder::create($row);
            }

            // 10) مسح مراجع الملفات من الإعدادات (لا نحذف الجداول)
            $fileSettings = [
                'site_logo', 'site_icon_file', 'license_image',
                'hero_background_image', 'hero_background_video',
                'popup_video_file', 'executive_director_image',
            ];
            foreach ($fileSettings as $key) {
                SiteSetting::setValue($key, '');
            }

            // 11) إعادة إعدادات لوحة التحكم
            $this->resetDashboardSettings();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('wesal')
                ->with('error', 'حدث خطأ أثناء إعادة الضبط: ' . $e->getMessage());
        }

        return redirect()->route('wesal')
            ->with('success', 'تم إعادة النظام إلى الحالة الافتراضية بنجاح. تم حذف القيود وطلبات الصرف والرسائل والوسائط والأخبار والمحتوى المضاف وإعادة الإعدادات.');
    }

    private function resetDashboardSettings(): void
    {
        $defaults = [
            'dashboard_primary_color' => '#5FB38E',
            'dashboard_primary_dark' => '#1F6B4F',
            'dashboard_secondary_color' => '#A8DCC3',
            'dashboard_accent_color' => '#5FB38E',
            'dashboard_sidebar_bg' => 'rgba(15, 61, 46, 0.95)',
            'dashboard_content_bg' => 'rgba(255, 255, 255, 0.05)',
            'dashboard_text_primary' => '#FFFFFF',
            'dashboard_text_secondary' => '#FFFFFF',
            'dashboard_border_color' => 'rgba(255, 255, 255, 0.1)',
            'dashboard_bg_gradient' => 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::setValue($key, $value);
        }

        SiteSetting::setValue('settings_updated_at', (string) time());
        \Illuminate\Support\Facades\Artisan::call('view:clear');
    }
}
