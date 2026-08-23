<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        Seo::set([
            'title' => __('site.blog.title'),
            'description' => __('site.blog.hero_lead'),
        ]);

        $category = $request->string('rubrique')->toString();
        $search = $request->string('q')->toString();

        $posts = Post::published()
            ->with(['category', 'author'])
            ->when($category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->when($search, function ($q) use ($search) {
                $q->where(fn ($sub) => $sub
                    ->where('title_fr', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('excerpt_fr', 'like', "%{$search}%"));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Post $post) => $post->toCard());

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'featured' => $category || $search
                ? null
                : Post::published()->with(['category', 'author'])->where('is_featured', true)->latest('published_at')->first()?->toCard(),
            'categories' => PostCategory::withCount(['posts' => fn ($q) => $q->published()])
                ->orderBy('position')
                ->get()
                ->map
                ->toArrayLocalised(),
            'filters' => ['rubrique' => $category ?: null, 'q' => $search ?: null],
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        $post->loadMissing(['category', 'author']);
        $post->incrementQuietly('views');

        Seo::set([
            'title' => $post->meta_title ?: $post->tr('title'),
            'description' => $post->meta_description ?: $post->tr('excerpt'),
            // Absolute-ised by the layout. Passed through Media so the
            // stored disk path becomes a real URL: the raw column is
            // 'blog/covers/x.jpg' and og:image would resolve it against the
            // document root, one directory short of the file.
            'image' => Media::url($post->cover_image),
            'type' => 'article',
            'published_at' => $post->published_at?->toIso8601String(),
        ]);

        return Inertia::render('Blog/Show', [
            'post' => $post->toDetail(),
            'related' => Post::published()
                ->with(['category', 'author'])
                ->where('post_category_id', $post->post_category_id)
                ->whereKeyNot($post->id)
                ->latest('published_at')
                ->limit(3)
                ->get()
                ->map
                ->toCard(),
        ]);
    }
}
