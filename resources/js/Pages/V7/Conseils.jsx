import { useMemo, useState } from 'react';
import { PageHead, useV7 } from '@/v7/core';
import { adviceList, ArticleCard } from '@/v7/sections';

export default function Conseils({ posts }) {
    const { L } = useV7();
    const [cat, setCat] = useState('');
    const cats = useMemo(() => [...new Map(posts.filter((p) => p.category).map((p) => [p.category.slug, p.category])).values()], [posts]);
    // Filtering by category only ever applies to written articles; the
    // announcements of the subjects still to come have no category record.
    const shown = cat ? posts.filter((p) => p.category?.slug === cat) : adviceList(L, posts);
    const toCome = useMemo(() => adviceList(L, posts).length - posts.length, [L, posts]);
    return (
        <>
            <PageHead crumbs={[[L.nav.conseils]]} eye={L.s10eye} title={L.s10title} lead={posts.length ? L.s10lead.split('.')[0] + '.' : L.s10lead} />
            <section className="f-sec f-wrap" style={{ paddingTop: 40, paddingBottom: 112 }} aria-labelledby="c-list">
                <h2 id="c-list" className="sr-only">{L.s10eye}</h2>
                {cats.length > 1 && (
                    <div role="group" aria-label={L.filterAria} className="chips scroll" style={{ marginBottom: 24 }}>
                        <button type="button" className="f-chip" aria-pressed={!cat} onClick={() => setCat('')}>{L.allCats}</button>
                        {cats.map((c) => (
                            <button key={c.slug} type="button" className="f-chip" aria-pressed={cat === c.slug} onClick={() => setCat(c.slug)}>{c.name}</button>
                        ))}
                    </div>
                )}
                {!posts.length && toCome > 0 && <p className="f-muted" style={{ margin: '0 0 24px' }}>{L.articlesSoon}</p>}
                <div className="g3">
                    {shown.map((p) => (
                        <ArticleCard key={p.slug ?? p.title} post={p} />
                    ))}
                </div>
            </section>
        </>
    );
}
