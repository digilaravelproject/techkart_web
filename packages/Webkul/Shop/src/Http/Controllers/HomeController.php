<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Http\Middleware\SecureHeaders;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Helpers\CatalogApiCache;
use Webkul\Shop\Http\Requests\ContactRequest;
use Webkul\Shop\Http\Resources\CategoryResource;
use Webkul\Shop\Http\Resources\CategoryTreeResource;
use Webkul\Shop\Http\Resources\ProductCardResource;
use Webkul\Shop\Mail\ContactUs;
use Webkul\Theme\Enums\SectionTypeEnum;
use Webkul\Theme\Repositories\SectionRepository;
use Webkul\Theme\ThemeCatalog;

class HomeController extends Controller
{
    /**
     * Using const variable for status.
     */
    public const STATUS = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SectionRepository $sectionRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository,
        protected CatalogApiCache $catalogApiCache
    ) {}

    /**
     * Loads the home page for the storefront.
     *
     * @return View
     */
    public function index()
    {
        $channel = core()->getCurrentChannel();

        $themeCode = themes()->current()?->code ?? $channel->theme;

        $sections = $this->sectionRepository->getRenderable(
            $channel->id,
            $themeCode
        );

        $categories = $this->categoryRepository->getVisibleCategoryTree($channel->root_category_id);

        $categories = CategoryTreeResource::collection($categories);

        $preloadedSections = $this->preloadCarouselData($sections, $channel->id);

        return view('shop::home.index', compact('sections', 'categories', 'preloadedSections'));
    }

    /**
     * Preload cached carousel data server-side so the storefront avoids concurrent API burst requests.
     */
    protected function preloadCarouselData($sections, int $channelId): array
    {
        $preloaded = [];

        foreach ($sections as $section) {
            $filters = (array) ($section->options['filters'] ?? []);

            if ($section->type === SectionTypeEnum::CATEGORY_CAROUSEL->value) {
                $params = array_merge([
                    'status' => 1,
                    'locale' => app()->getLocale(),
                ], $filters);

                $cached = $this->catalogApiCache->remember('categories', $params, function () use ($params) {
                    return CategoryResource::collection($this->categoryRepository->getAll($params))
                        ->response()
                        ->getData(true);
                });

                $preloaded[$section->id] = $cached['data'] ?? [];
            } elseif ($section->type === SectionTypeEnum::PRODUCT_CAROUSEL->value) {
                $cached = $this->catalogApiCache->remember('products', $filters, function () use ($filters, $channelId) {
                    $params = array_merge($filters, [
                        'query' => '',
                        'channel_id' => $channelId,
                        'status' => 1,
                        'visible_individually' => 1,
                    ]);

                    return ProductCardResource::collection($this->productRepository->getAll($params))
                        ->response()
                        ->getData(true);
                });

                $preloaded[$section->id] = $cached['data'] ?? [];
            }
        }

        return $preloaded;
    }

    /**
     * Render a channel's home page in the requested theme from unpublished section edits, for the
     * appearance area.
     *
     * @return View
     */
    public function preview()
    {
        abort_unless(bouncer()->hasPermission('appearance.sections'), 403);

        $channel = core()->getAllChannels()->firstWhere('id', (int) request('channel'))
            ?? core()->getCurrentChannel();

        $themeCode = $this->previewedTheme($channel->theme);

        request()->attributes->set(SecureHeaders::FRAMABLE, true);

        request()->attributes->set(SectionRepository::PREVIEWING, true);

        $previewed = clone $channel;

        $previewed->theme = $themeCode;

        core()->setCurrentChannel($previewed);

        themes()->set($themeCode);

        $sections = $this->sectionRepository->getDraftedForPreview(
            $channel->id,
            $themeCode,
            app()->getLocale()
        );

        $categories = CategoryTreeResource::collection(
            $this->categoryRepository->getVisibleCategoryTree($channel->root_category_id)
        );

        return view('shop::home.index', compact('sections', 'categories') + [
            'preview' => true,
            'previewTheme' => app(ThemeCatalog::class)->find($themeCode)['name'] ?? $themeCode,
        ]);
    }

    /**
     * Loads the home page for the storefront if something wrong.
     *
     * @return \Exception
     */
    public function notFound()
    {
        abort(404);
    }

    /**
     * Display the contact us page.
     *
     * @return View
     */
    public function contactUs()
    {
        return view('shop::home.contact-us');
    }

    /**
     * Send the contact us mail.
     *
     * @return RedirectResponse
     */
    public function sendContactUsMail(ContactRequest $contactRequest)
    {
        try {
            Mail::queue(new ContactUs($contactRequest->only([
                'name',
                'email',
                'contact',
                'message',
            ])));

            session()->flash('success', trans('shop::app.home.thanks-for-contact'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            report($e);
        }

        return back();
    }

    /**
     * The theme to preview, which has to be installed when named, otherwise the one the channel runs.
     */
    protected function previewedTheme(?string $channelTheme): string
    {
        $requested = request('theme');

        if (filled($requested)) {
            abort_unless(
                is_string($requested)
                && app(ThemeCatalog::class)->isInstalled($requested),
                404
            );

            return $requested;
        }

        if (
            $channelTheme
            && app(ThemeCatalog::class)->isInstalled($channelTheme)
        ) {
            return $channelTheme;
        }

        return config('themes.shop-default');
    }
}
