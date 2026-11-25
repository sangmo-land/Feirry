import { useCallback, useEffect, useRef, useState } from 'react';
import { CloseButton, Dialog, Icon, Img, useV7 } from './core';

/** Every picture of a project in viewing order: the cover first, then the gallery. */
export function projectImages(p) {
    return [p?.cover_image, ...(p?.gallery ?? [])].filter(Boolean);
}

/**
 * Lightbox: one picture at a time, whole (never cropped).
 *
 * Motion: the picture zooms in on opening, then each change slides the old
 * picture out and the new one in, in the direction of travel. On a phone the
 * picture follows the finger and settles back if the swipe is too short.
 * Arrow keys, Home / End, the side arrows and the film strip all move between
 * pictures; Escape, the close button or a tap on the dark surround close it.
 * `index === null` = closed.
 */
export function Lightbox({ images, index, title, onIndex, onClose }) {
    const { L } = useV7();
    const count = images.length;
    const open = index !== null && index >= 0 && index < count;

    // `leaving`: the picture on its way out; `dir`: +1 forward, -1 back, 0 just opened;
    // `from`: where the finger released it, so the exit starts from that spot.
    const [motion, setMotion] = useState({ leaving: null, dir: 0, from: 0 });
    const [drag, setDrag] = useState(0);
    const [dragging, setDragging] = useState(false);
    const touch = useRef(null);
    const strip = useRef(null);

    useEffect(() => {
        if (!open) {
            setMotion({ leaving: null, dir: 0, from: 0 });
            setDrag(0);
            setDragging(false);
        }
    }, [open]);

    // The neighbours are fetched ahead so a change never shows an empty frame.
    useEffect(() => {
        if (!open || count < 2) return;
        [index + 1, index - 1].forEach((i) => {
            const picture = new window.Image();
            picture.src = images[(i + count) % count];
        });
    }, [open, index, count, images]);

    // Keeps the current thumbnail in view inside the strip only: never scrolls the page.
    useEffect(() => {
        const bar = strip.current;
        const current = bar?.querySelector('[aria-current="true"]');
        if (!bar || !current) return;
        bar.scrollTo({ left: current.offsetLeft - (bar.clientWidth - current.clientWidth) / 2, behavior: 'smooth' });
    }, [open, index]);

    const show = useCallback(
        (target, dir, from = 0) => {
            if (!open || count < 2) return;
            const next = (target + count) % count;
            if (next === index) return;
            setMotion({ leaving: index, dir, from });
            onIndex(next);
        },
        [open, count, index, onIndex],
    );
    const step = useCallback((dir, from = 0) => show(index + dir, dir, from), [show, index]);

    const onKeyDown = (e) => {
        if (count < 2) return;
        const moves = { ArrowRight: () => step(1), ArrowLeft: () => step(-1), Home: () => show(0, -1), End: () => show(count - 1, 1) };
        if (moves[e.key]) {
            e.preventDefault();
            moves[e.key]();
        }
    };

    const onTouchStart = (e) => {
        if (e.touches.length !== 1) return;
        touch.current = { x: e.touches[0].clientX, y: e.touches[0].clientY, axis: null };
    };
    const onTouchMove = (e) => {
        const start = touch.current;
        if (!start || e.touches.length !== 1) return;
        const dx = e.touches[0].clientX - start.x;
        const dy = e.touches[0].clientY - start.y;
        if (!start.axis && Math.abs(dx) + Math.abs(dy) > 8) start.axis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
        if (start.axis === 'x') {
            setDragging(true);
            // A single picture resists the finger instead of following it.
            setDrag(count > 1 ? dx : dx / 4);
        }
    };
    const onTouchEnd = () => {
        const start = touch.current;
        touch.current = null;
        setDragging(false);
        if (!start || start.axis !== 'x') return;
        if (count > 1 && Math.abs(drag) > 56) step(drag < 0 ? 1 : -1, drag);
        setDrag(0);
    };

    const way = motion.dir > 0 ? 'next' : 'prev';
    const label = (i) => `${L.galImage} ${i + 1} ${L.galOf} ${count}`;

    return (
        <Dialog open={open} onClose={onClose} label={`${L.galEye} : ${title}`} className="f-modal f-lightbox" panelClass="f-lightbox-panel">
            {open && (
                <div className="lb-stage" onKeyDown={onKeyDown}>
                    <p className="lb-count" aria-live="polite">
                        <strong>{index + 1}</strong> / {count}
                        <span className="lb-title">{title}</span>
                    </p>
                    <CloseButton overlay onClick={onClose} label={L.galClose} />
                    <div className="lb-view" onClick={(e) => !e.target.closest('img, button') && onClose()} onTouchStart={onTouchStart} onTouchMove={onTouchMove} onTouchEnd={onTouchEnd} onTouchCancel={onTouchEnd}>
                        {motion.leaving !== null && (
                            <div key={`out-${motion.leaving}`} className={`lb-slide lb-leave-${way}`} style={{ '--lb-from': `${motion.from}px` }} onAnimationEnd={() => setMotion((m) => ({ ...m, leaving: null }))} aria-hidden="true">
                                <div className="lb-drag">
                                    <img className="lb-img" src={images[motion.leaving]} alt="" />
                                </div>
                            </div>
                        )}
                        <div key={`in-${index}`} className={`lb-slide ${motion.dir === 0 ? 'lb-enter-zoom' : `lb-enter-${way}`}`}>
                            <div className="lb-drag" style={{ transform: drag ? `translate3d(${drag}px, 0, 0)` : undefined, transition: dragging ? 'none' : undefined }}>
                                <img className="lb-img" src={images[index]} alt={`${title} : ${label(index)}`} decoding="async" draggable="false" />
                            </div>
                        </div>
                        {count > 1 && (
                            <>
                                <button type="button" className="lb-nav prev" onClick={() => step(-1)} aria-label={L.galPrev}>
                                    <Icon name="arrowL" size={20} stroke={2.2} />
                                </button>
                                <button type="button" className="lb-nav next" onClick={() => step(1)} aria-label={L.galNext}>
                                    <Icon name="arrow" size={20} stroke={2.2} />
                                </button>
                            </>
                        )}
                    </div>
                    {count > 1 && (
                        <div className="lb-strip" ref={strip} role="group" aria-label={L.galEye}>
                            {images.map((src, i) => (
                                <button key={src + i} type="button" className="lb-thumb" aria-current={i === index ? 'true' : undefined} aria-label={label(i)} onClick={() => show(i, i > index ? 1 : -1)}>
                                    <Img src={src} sizes="72px" alt="" />
                                </button>
                            ))}
                        </div>
                    )}
                </div>
            )}
        </Dialog>
    );
}

/**
 * A cover picture that opens the lightbox when clicked or tapped anywhere.
 * The button sits under the badges (`children`), so they stay readable and
 * the whole surface remains one target.
 */
export function ZoomCover({ src, title, pos, sizes, priority, count = 1, onOpen, height, className = '', style, children }) {
    const { L } = useV7();
    const many = count > 1;
    return (
        <div className={`f-media f-tile ${className}`} style={{ height, ...style }}>
            <button type="button" className="f-zoom" onClick={onOpen} aria-haspopup="dialog" aria-label={many ? `${L.galSee} ${count} ${L.galImages} : ${title}` : `${L.galOpen} : ${title}`}>
                <Img src={src} alt="" pos={pos} sizes={sizes} priority={priority} />
            </button>
            {children}
            <span className="f-pill f-zoom-hint" style={{ right: 16, bottom: 16 }} aria-hidden="true">
                <Icon name="expand" size={14} stroke={2.2} />
                {many ? `${count} ${L.galImages}` : L.galZoom}
            </span>
        </div>
    );
}

/**
 * Every gallery picture as a tile. `onOpen(i)` receives the tile's position in
 * `images`; the page adds its own offset when the cover comes first.
 * Nothing is capped: six uploaded pictures show six tiles.
 */
export function GalleryGrid({ images, pos, onOpen, total }) {
    const { L } = useV7();
    if (!images?.length) return null;
    const all = total ?? images.length;
    const shift = all - images.length;

    return (
        <ul className="gal-grid">
            {images.map((src, i) => (
                <li key={src + i} style={{ '--i': i }}>
                    <button type="button" className="gal-tile f-tile" onClick={() => onOpen(i)} aria-haspopup="dialog" aria-label={`${L.galOpen} ${i + 1 + shift} ${L.galOf} ${all}`}>
                        <Img src={src} pos={pos} sizes="(max-width: 767px) 50vw, 33vw" alt="" />
                    </button>
                </li>
            ))}
        </ul>
    );
}

/** Thumbnails that jump straight into the lightbox at the chosen picture. */
export function GalleryStrip({ images, from = 0, onOpen }) {
    const { L } = useV7();
    if (!images?.length) return null;
    const total = images.length + from;
    return (
        <div className="gal-strip" role="group" aria-label={L.galEye}>
            {images.map((src, i) => (
                <button key={src + i} type="button" className="gal-thumb f-tile" aria-haspopup="dialog" aria-label={`${L.galOpen} ${i + 1 + from} ${L.galOf} ${total}`} onClick={() => onOpen(i + from)}>
                    <Img src={src} sizes="96px" alt="" />
                </button>
            ))}
        </div>
    );
}
