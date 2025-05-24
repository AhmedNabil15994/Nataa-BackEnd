<?php

namespace Modules\Catalog\ViewComposers\FrontEnd;

use Modules\Catalog\Repositories\FrontEnd\CategoryRepository as Category;
use Illuminate\View\View;
use Cache;

class CategoryComposer
{
    protected $headerCategories;
    protected $categories;

    public function __construct(Category $category)
    {
        $this->headerCategories = $category->getHeaderCategories();
        $this->categories = $category->getAllActive();
    }

    public function compose(View $view)
    {
        $view->with([
            'headerCategories' => $this->headerCategories,
            'categories' => $this->categories,
        ]);
    }
}
