<?php

namespace App\Services;

use App\Models\Category;
use Exception;

class CategoryService
{
    /**
     * Membuat kategori baru ke database.
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Memperbarui kategori yang ada.
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Menghapus kategori, dengan pengaman jika masih ada item terkait.
     * @throws Exception
     */
    public function deleteCategory(Category $category): void
    {
        if ($category->items()->exists()) {
            throw new Exception('Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        $category->delete();
    }
}
