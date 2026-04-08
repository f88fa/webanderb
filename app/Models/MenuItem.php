<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $table = 'menu_items';

    protected $fillable = [
        'title',
        'type',
        'url',
        'parent_id',
        'page_content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get child menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active child menu items
     */
    public function activeChildren()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get all root menu items (no parent) ordered
     */
    public static function getRootItems()
    {
        return self::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get root menu items excluding "الرئيسية" (Home) item
     * Used for navigation menu in all pages
     */
    public static function getRootItemsExcludingHome()
    {
        return self::whereNull('parent_id')
            ->where('is_active', true)
            ->where(function($query) {
                // Exclude only "الرئيسية" by title
                $query->where('title', '!=', 'الرئيسية');
            })
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all menu items ordered (for admin)
     */
    public static function getAllOrdered()
    {
        return self::orderBy('order')->get();
    }

    /**
     * Get menu item by slug or id for page type
     */
    public static function getPageBySlug($slug)
    {
        return self::where('type', 'page')
            ->where('is_active', true)
            ->where(function($query) use ($slug) {
                $query->where('url', $slug)
                      ->orWhere('id', $slug);
            })
            ->first();
    }
}
