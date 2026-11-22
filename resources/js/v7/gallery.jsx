import { useCallback, useEffect, useRef, useState } from 'react';
import { CloseButton, Dialog, Icon, Img, useV7 } from './core';

/**
 * Lightbox: one picture at a time, whole (never cropped), with previous / next.
 *
 * Arrow keys and a horizontal swipe move between pictures; Escape, the close
 * button or a click on the dark surround close it. `index === null` = closed.
 */
export function Lightbox({ images, index, title, onIndex, onClose }) {
    const { L } = useV7();
    const open = index !== null && index >= 0 && index < images.length;
    const count = images.length;
    const touchX = useRef(null);

    const go = useCallback((step) => onIndex((index + step + count) % count), [index, count, onIndex]);

    const onKeyDown = (e) => {
        if (count < 2) return;
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            go(1);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            go(-1);
        }
    };

    return (
        <Dialog open={open} onClose={onClose} label={`${L.galEye} : ${title}`} className="f-modal f-lightbox" panelClass="f-lightbox-panel">
            {open && (
                <div
                    className="f-lightbox-stage"
                    onKeyDown={onKeyDown}
                    onClick={(e) => e.target === e.currentTarget && onClose()}
                    onTouchStart={(e) => {
                        touchX.current = e.touches[0]?.clientX ?? null;
                    }}
                    onTouchEnd={(e) => {
                        const start = touchX.current;
                        touchX.current = null;
                        if (start === null || count < 2) return;
                        const delta = (e.changedTouches[0]?.clientX ?? start) - start;
                        if (Math.abs(delta) > 48) go(delta < 0 ? 1 : -1);
                    }}
                >
                    <CloseButton overlay onClick={onClose} label={L.galClose} />
                    <img className="f-lightbox-img" src={images[index]} alt={`${title} : ${L.galImage} ${index + 1} ${L.galOf} ${count}`} decoding="async" />
                    {count > 1 && (
                        <>
                            <button type="button" className="f-lightbox-nav prev" onClick={() => go(-1)} aria-label={L.galPrev}>
                                <Icon name="arrowL" size={20} stroke={2.2} />
                            </button>
                            <button type="button" className="f-lightbox-nav next" onClick={() => go(1)} aria-label={L.galNext}>
                                <Icon name="arrow" size={20} stroke={2.2} />
                            </button>
                        </>
                    )}
                    <p className="f-lightbox-count" aria-live="polite">
                        {index + 1} / {count}
                    </p>
                </div>
            )}
        </Dialog>
    );
}

/**
 * Every picture of a project, as tiles that open the lightbox.
 * Nothing is capped: six uploaded pictures show six tiles.
 */
export function GalleryGrid({ images, title, pos }) {
    const { L } = useV7();
    const [index, setIndex] = useState(null);
    const close = useCallback(() => setIndex(null), []);

    if (!images?.length) return null;

    return (
        <>
            <ul className="gal-grid">
                {images.map((src, i) => (
                    <li key={src + i}>
                        <button type="button" className="gal-tile f-tile" onClick={() => setIndex(i)} aria-haspopup="dialog" aria-label={`${L.galOpen} ${i + 1} ${L.galOf} ${images.length}`}>
                            <Img src={src} pos={pos} sizes="(max-width: 767px) 50vw, 33vw" alt="" />
                        </button>
                    </li>
                ))}
            </ul>
            <Lightbox images={images} index={index} title={title} onIndex={setIndex} onClose={close} />
        </>
    );
}

/**
 * Compact viewer for the project preview dialog: the cover, then a strip of
 * thumbnails that swap the large picture in place.
 */
export function MediaSwitcher({ cover, images, title, pos, height = 260, children }) {
    const { L } = useV7();
    const all = [cover, ...(images ?? [])].filter(Boolean);
    const [shown, setShown] = useState(0);

    // A different project in the same dialog starts again from its cover.
    useEffect(() => setShown(0), [cover]);

    const current = Math.min(shown, all.length - 1);

    return (
        <>
            <div className="f-media" style={{ height }}>
                <Img src={all[current]} alt={current === 0 ? title : `${title} : ${L.galImage} ${current + 1} ${L.galOf} ${all.length}`} pos={current === 0 ? pos : 'center 50%'} />
                {children}
                {all.length > 1 && (
                    <span className="f-pill" style={{ right: 16, bottom: 16 }} aria-hidden="true">
                        {current + 1} / {all.length}
                    </span>
                )}
            </div>
            {all.length > 1 && (
                <div className="gal-strip" role="group" aria-label={L.galEye}>
                    {all.map((src, i) => (
                        <button key={src + i} type="button" className="gal-thumb" aria-pressed={i === current} aria-label={`${L.galImage} ${i + 1} ${L.galOf} ${all.length}`} onClick={() => setShown(i)}>
                            <Img src={src} sizes="96px" alt="" />
                        </button>
                    ))}
                </div>
            )}
        </>
    );
}
