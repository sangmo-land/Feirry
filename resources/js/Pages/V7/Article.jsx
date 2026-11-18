import { A, Icon, Img, PageHead, formatDate, useV7 } from '@/v7/core';
import { ArticleCard } from '@/v7/sections';

export default function Article({ post, related = [] }) {
    const { L, locale } = useV7();
    return (
        <>
            <PageHead crumbs={[[L.nav.conseils, '/conseils'], [post.title]]} eye={post.category?.name ?? L.s10eye} title={post.title} lead={post.excerpt}>
                <p className="f-muted" style={{ marginTop: 16, fontSize: 14 }}>
                    {L.by} {post.author?.name ?? 'FEIRRY'} · {formatDate(post.published_at, locale)}
                    {post.updated_at && post.updated_at !== post.published_at ? ` · ${L.updated} ${formatDate(post.updated_at, locale)}` : ''}
                </p>
            </PageHead>
            {post.cover_image && (
                <div className="f-wrap" style={{ paddingTop: 24 }}>
                    <div className="f-card f-media" style={{ aspectRatio: '3 / 2', maxHeight: 560, width: '100%' }}>
                        <Img src={post.cover_image} alt="" sizes="100vw" priority />
                    </div>
                </div>
            )}
            <article className="f-wrap" style={{ paddingTop: 40 }}>
                <div className="f-prose" style={{ maxWidth: 760 }} dangerouslySetInnerHTML={{ __html: post.body ?? '' }} />
                <div className="f-card f-panel" style={{ maxWidth: 760, marginTop: 32, padding: 24, display: 'flex', gap: 16, alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' }}>
                    <strong>{L.projectCta}</strong>
                    <A className="f-btn f-btn-primary f-btn-inline" href="/mon-projet">{L.ctaPrimary}</A>
                </div>
            </article>
            {related.length > 0 && (
                <section className="f-sec f-wrap" aria-labelledby="rel">
                    <h2 id="rel" className="f-h2 f-h2s" style={{ marginBottom: 24 }}>{L.related}</h2>
                    <div className="g3">{related.map((p) => <ArticleCard key={p.slug} post={p} />)}</div>
                </section>
            )}
            <div className="f-wrap" style={{ padding: '32px 32px 112px' }}>
                <A className="f-textbtn" href="/conseils">
                    <Icon name="arrowL" size={16} />
                    {L.allArticles}
                </A>
            </div>
        </>
    );
}
