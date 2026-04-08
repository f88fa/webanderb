<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Show menu page in dashboard
     */
    public function index()
    {
        $menuItems = MenuItem::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'menu',
            'menuItems' => $menuItems,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new menu item
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:link,dropdown,page',
            'url' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'page_content' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'type', 'url', 'parent_id', 'page_content', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // For dropdown type, parent_id should be null (it's a parent)
        if ($data['type'] === 'dropdown') {
            $data['parent_id'] = null;
            $data['url'] = null;
            $data['page_content'] = null;
        }

        // For page type, generate URL from title if not provided
        if ($data['type'] === 'page' && empty($data['url'])) {
            $data['url'] = 'page/' . \Illuminate\Support\Str::slug($data['title'], '-');
        }

        // For link type, ensure URL is provided
        if ($data['type'] === 'link' && empty($data['url'])) {
            return back()->withErrors(['url' => 'الرابط مطلوب للنوع رابط'])->withInput();
        }

        MenuItem::create($data);

        return back()->with('success_message', 'تم إضافة عنصر القائمة بنجاح!');
    }

    /**
     * Update a menu item
     */
    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:link,dropdown,page',
            'url' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'page_content' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'type', 'url', 'parent_id', 'page_content', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Prevent setting parent to itself or its children
        if (!empty($data['parent_id']) && $data['parent_id'] == $id) {
            return back()->withErrors(['parent_id' => 'لا يمكن تعيين العنصر كوالد لنفسه'])->withInput();
        }

        // Check if parent is a child of this item (circular reference)
        if (!empty($data['parent_id'])) {
            $parent = MenuItem::find($data['parent_id']);
            if ($parent && $parent->parent_id == $id) {
                return back()->withErrors(['parent_id' => 'لا يمكن إنشاء مرجع دائري'])->withInput();
            }
        }

        // For dropdown type, parent_id should be null
        if ($data['type'] === 'dropdown') {
            $data['parent_id'] = null;
            $data['url'] = null;
            $data['page_content'] = null;
        }

        // For page type, generate URL from title if not provided
        if ($data['type'] === 'page' && empty($data['url'])) {
            $data['url'] = 'page/' . \Illuminate\Support\Str::slug($data['title'], '-');
        }

        // For link type, ensure URL is provided
        if ($data['type'] === 'link' && empty($data['url'])) {
            return back()->withErrors(['url' => 'الرابط مطلوب للنوع رابط'])->withInput();
        }

        $menuItem->update($data);

        return back()->with('success_message', 'تم تحديث عنصر القائمة بنجاح!');
    }

    /**
     * Delete a menu item
     */
    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        
        // Delete all children first
        $menuItem->children()->delete();
        
        $menuItem->delete();

        return back()->with('success_message', 'تم حذف عنصر القائمة بنجاح!');
    }
}
