<?php

namespace App\Http\Controllers;

use App\Models\VisionMission;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisionMissionController extends Controller
{
    /**
     * Show vision and mission page in dashboard
     */
    public function index()
    {
        $visionMission = VisionMission::getLatest();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'vision-mission',
            'visionMission' => $visionMission,
            'settings' => $settings
        ]);
    }

    /**
     * Store or update vision and mission content
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'vision_icon' => 'nullable|string|max:255',
            'mission_icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if record exists
        $visionMission = VisionMission::getLatest();
        
        $data = $request->only(['section_title', 'vision', 'mission', 'vision_icon', 'mission_icon']);
        
        // Set default icons if not provided
        if (empty($data['vision_icon'])) {
            $data['vision_icon'] = 'fas fa-eye';
        }
        if (empty($data['mission_icon'])) {
            $data['mission_icon'] = 'fas fa-bullseye';
        }
        
        if ($visionMission) {
            // Update existing
            $visionMission->update($data);
        } else {
            // Create new
            VisionMission::create($data);
        }

        return back()->with('success_message', 'تم حفظ الرؤية والرسالة بنجاح!');
    }
}
