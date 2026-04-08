<?php

namespace App\Http\Controllers;

use App\Models\SectionOrder;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SectionOrderController extends Controller
{
    /**
     * Show section order page in dashboard
     */
    public function index()
    {
        $sections = SectionOrder::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'section-order',
            'sections' => $sections,
            'settings' => $settings
        ]);
    }

    /**
     * Update section order and visibility
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sections' => 'required|array',
            'sections.*.key' => 'required|string',
            'sections.*.order' => 'required|integer',
            'sections.*.visible' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sections = $request->input('sections');
            
            DB::beginTransaction();
            
            foreach ($sections as $section) {
                $key = $section['key'] ?? '';
                $sectionOrder = SectionOrder::where('section_key', $key)->first();

                if ($sectionOrder) {
                    $sectionOrder->update([
                        'order' => (int)$section['order'],
                        'is_visible' => (bool)$section['visible'],
                    ]);
                } elseif (str_starts_with($key, 'banner_section_')) {
                    $id = (int) str_replace('banner_section_', '', $key);
                    if ($id > 0) {
                        SectionOrder::create([
                            'section_key' => $key,
                            'section_name' => 'بانر: ' . $id,
                            'order' => (int)$section['order'],
                            'is_visible' => (bool)$section['visible'],
                        ]);
                    }
                } else {
                    Log::warning('Section not found: ' . $key);
                }
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب الأقسام بنجاح!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Section Order Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset section order to default values
     */
    public function reset()
    {
        try {
            $defaultSections = [
                ['section_key' => 'about', 'section_name' => 'من نحن', 'order' => 1, 'is_visible' => true],
                ['section_key' => 'vision_mission', 'section_name' => 'الرؤية والرسالة', 'order' => 2, 'is_visible' => true],
                ['section_key' => 'services', 'section_name' => 'الخدمات', 'order' => 3, 'is_visible' => true],
                ['section_key' => 'projects', 'section_name' => 'المشاريع', 'order' => 4, 'is_visible' => true],
                ['section_key' => 'media', 'section_name' => 'المركز الإعلامي', 'order' => 5, 'is_visible' => true],
                ['section_key' => 'testimonials', 'section_name' => 'ماذا قالوا عنا', 'order' => 6, 'is_visible' => true],
                ['section_key' => 'partners', 'section_name' => 'الشركاء', 'order' => 7, 'is_visible' => true],
                ['section_key' => 'news', 'section_name' => 'الأخبار', 'order' => 8, 'is_visible' => true],
                ['section_key' => 'staff', 'section_name' => 'الموظفين', 'order' => 9, 'is_visible' => true],
            ];

            DB::beginTransaction();
            
            foreach ($defaultSections as $section) {
                $sectionOrder = SectionOrder::where('section_key', $section['section_key'])->first();

                if ($sectionOrder) {
                    $sectionOrder->update([
                        'order' => (int)$section['order'],
                        'is_visible' => (bool)$section['is_visible'],
                    ]);
                } else {
                    SectionOrder::create([
                        'section_key' => $section['section_key'],
                        'section_name' => $section['section_name'],
                        'order' => (int)$section['order'],
                        'is_visible' => (bool)$section['is_visible'],
                    ]);
                }
            }

            SectionOrder::where('section_key', SectionOrder::LEGACY_BANNER_SECTIONS_KEY)->delete();
            SectionOrder::syncBannerSections();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة الإعدادات الافتراضية بنجاح!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Section Order Reset Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة التعيين: ' . $e->getMessage()
            ], 500);
        }
    }
}
