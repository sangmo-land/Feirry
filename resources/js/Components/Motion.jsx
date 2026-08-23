import { Children, cloneElement, isValidElement } from 'react';
import { motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useReducedMotion } from '@/Hooks';

/* ------------------------------------------------------------------ */
/* Reveal: the workhorse. Fades and lifts a block once it scrolls in.  */
/* ------------------------------------------------------------------ */
export function Reveal({
    children,
    as: Tag = 'div',
    delay = 0,
    y = 28,
    x = 0,
    duration = 0.9,
    once = true,
    amount = 0.25,
    className,
    ...rest
}) {
    const reduced = useReducedMotion();
    const MotionTag = motion[Tag] ?? motion.div;

    if (reduced) {
        return (
            <Tag className={className} {...rest}>
                {children}
            </Tag>
        );
    }

    return (
        <MotionTag
            className={className}
            initial={{ opacity: 0, y, x }}
            whileInView={{ opacity: 1, y: 0, x: 0 }}
            viewport={{ once, amount }}
            transition={{ duration, delay, ease: EASE.outExpo }}
            {...rest}
        >
            {children}
        </MotionTag>
    );
}

/* ------------------------------------------------------------------ */
/* Stagger: reveals direct children one after another.                 */
/* ------------------------------------------------------------------ */
export function Stagger({
    children,
    className,
    delay = 0,
    stagger = 0.08,
    y = 24,
    amount = 0.2,
    as: Tag = 'div',
    ...rest
}) {
    const reduced = useReducedMotion();
    const MotionTag = motion[Tag] ?? motion.div;

    if (reduced) {
        return (
            <Tag className={className} {...rest}>
                {children}
            </Tag>
        );
    }

    return (
        <MotionTag
            className={className}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount }}
            variants={{
                hidden: {},
                visible: { transition: { staggerChildren: stagger, delayChildren: delay } },
            }}
            {...rest}
        >
            {Children.map(children, (child, index) =>
                isValidElement(child) ? (
                    <StaggerItem key={child.key ?? index} y={y}>
                        {child}
                    </StaggerItem>
                ) : (
                    child
                ),
            )}
        </MotionTag>
    );
}

function StaggerItem({ children, y }) {
    return (
        <motion.div
            variants={{
                hidden: { opacity: 0, y },
                visible: { opacity: 1, y: 0, transition: { duration: 0.85, ease: EASE.outExpo } },
            }}
        >
            {children}
        </motion.div>
    );
}

/* ------------------------------------------------------------------ */
/* SplitText: word-by-word reveal from behind a clipping mask.         */
/* This is the signature heading animation across the site.            */
/* ------------------------------------------------------------------ */
export function SplitText({
    text = '',
    as: Tag = 'span',
    className,
    wordClassName,
    delay = 0,
    stagger = 0.045,
    duration = 1,
    once = true,
    ...rest
}) {
    const reduced = useReducedMotion();
    const words = String(text).split(' ').filter(Boolean);

    if (reduced) {
        return (
            <Tag className={className} {...rest}>
                {text}
            </Tag>
        );
    }

    const MotionTag = motion[Tag] ?? motion.span;

    return (
        <MotionTag
            className={cn('inline', className)}
            initial="hidden"
            whileInView="visible"
            viewport={{ once, amount: 0.4 }}
            variants={{
                hidden: {},
                visible: { transition: { staggerChildren: stagger, delayChildren: delay } },
            }}
            aria-label={text}
            {...rest}
        >
            {words.map((word, index) => (
                <span
                    key={`${word}-${index}`}
                    className="inline-block overflow-hidden align-bottom"
                    aria-hidden="true"
                >
                    <motion.span
                        className={cn('inline-block', wordClassName)}
                        variants={{
                            hidden: { y: '110%', rotate: 3 },
                            visible: {
                                y: '0%',
                                rotate: 0,
                                transition: { duration, ease: EASE.outExpo },
                            },
                        }}
                    >
                        {word}
                        {index < words.length - 1 ? ' ' : ''}
                    </motion.span>
                </span>
            ))}
        </MotionTag>
    );
}

/* ------------------------------------------------------------------ */
/* ClipReveal: uncovers an element with a wipe. Used on imagery.       */
/*                                                                     */
/* The clip lives on an inner element, never on the one being watched. */
/* Chrome folds clip-path and transforms into the rectangle it hands   */
/* IntersectionObserver, so an element that starts clipped (or scaled) */
/* to zero area reports 0% visible forever and whileInView never runs. */
/* Watching an untransformed wrapper avoids that deadlock.             */
/* ------------------------------------------------------------------ */
export function ClipReveal({
    children,
    className,
    direction = 'up',
    duration = 1.25,
    delay = 0,
    amount = 0.25,
    ...rest
}) {
    const reduced = useReducedMotion();

    const start = {
        up: 'inset(100% 0% 0% 0%)',
        down: 'inset(0% 0% 100% 0%)',
        left: 'inset(0% 100% 0% 0%)',
        right: 'inset(0% 0% 0% 100%)',
    }[direction];

    if (reduced) {
        return (
            <div className={className} {...rest}>
                {children}
            </div>
        );
    }

    return (
        <motion.div
            className={className}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount }}
            {...rest}
        >
            <motion.div
                className="overflow-hidden"
                variants={{
                    hidden: { clipPath: start },
                    visible: { clipPath: 'inset(0% 0% 0% 0%)' },
                }}
                transition={{ duration, delay, ease: EASE.inOutQuint }}
            >
                {children}
            </motion.div>
        </motion.div>
    );
}

/* ------------------------------------------------------------------ */
/* Bar: a track that fills itself in. Backs Rule, HazardBar and the    */
/* project progress meters. Same watched-wrapper rule as ClipReveal:   */
/* the track keeps its full size, only the fill inside is scaled.      */
/* ------------------------------------------------------------------ */
export function Bar({
    className,
    fillClassName,
    to = 1,
    delay = 0,
    duration = 1.4,
    vertical = false,
    amount = 0.4,
}) {
    const reduced = useReducedMotion();

    if (reduced) {
        return (
            <div className={className}>
                <div
                    className={cn('h-full w-full', fillClassName)}
                    style={vertical ? { transform: `scaleY(${to})` } : { transform: `scaleX(${to})` }}
                />
            </div>
        );
    }

    return (
        <motion.div
            className={className}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount }}
        >
            <motion.div
                className={cn('h-full w-full', vertical ? 'origin-top' : 'origin-left', fillClassName)}
                variants={{
                    hidden: vertical ? { scaleY: 0 } : { scaleX: 0 },
                    visible: vertical ? { scaleY: to } : { scaleX: to },
                }}
                transition={{ duration, delay, ease: EASE.inOutQuint }}
            />
        </motion.div>
    );
}

/* ------------------------------------------------------------------ */
/* Rule: a hairline that draws itself in. Used under section headings. */
/* ------------------------------------------------------------------ */
export function Rule({ className, delay = 0, duration = 1.4, vertical = false }) {
    return (
        <Bar
            className={cn('bg-concrete-200', vertical ? 'w-px' : 'h-px', className)}
            fillClassName="bg-concrete-400"
            vertical={vertical}
            delay={delay}
            duration={duration}
            amount={0.1}
        />
    );
}

/* ------------------------------------------------------------------ */
/* WipeText: the header's signature treatment, shared by the logo      */
/* slogan and the nav links so the bar reads as one object.            */
/*                                                                     */
/* Two gestures, both travelling left to right — the same direction    */
/* the logo mark's own sweep runs:                                     */
/*   - once on mount, each word rises out of its own mask;             */
/*   - while an enclosing `group/wipe` is hovered, a second copy in    */
/*     the accent colour wipes across the line.                        */
/*                                                                     */
/* The wiped copy repeats the mask markup instead of setting the       */
/* string as plain text. Identical boxes are the only way the two      */
/* layers stay in register: a bare string re-resolves the inter-word   */
/* spacing and the overlay drifts off the copy underneath it.          */
/*                                                                     */
/* Both layers are aria-hidden, so the caller owns the accessible      */
/* name — an aria-label on the link or button doing the wrapping.      */
/* ------------------------------------------------------------------ */
const WIPE_RISE = {
    hidden: { y: '115%', skewY: 6 },
    visible: { y: '0%', skewY: 0, transition: { duration: 0.85, ease: EASE.outExpo } },
};

function WipeWords({ text, animated = false }) {
    const words = String(text).split(/\s+/).filter(Boolean);

    return words.map((word, index) => {
        // A non-breaking space keeps the gap inside the masked box: a plain one
        // at the end of an inline-block is stripped by white-space processing,
        // which runs the words together.
        const content = index < words.length - 1 ? `${word}\u00A0` : word;

        return (
            <span key={`${word}-${index}`} className="inline-block overflow-hidden align-bottom">
                {animated ? (
                    <motion.span className="inline-block" variants={WIPE_RISE}>
                        {content}
                    </motion.span>
                ) : (
                    <span className="inline-block">{content}</span>
                )}
            </span>
        );
    });
}

export function WipeText({
    text,
    typeClassName,
    restClassName,
    wipeClassName,
    delay = 0,
    stagger = 0.055,
    className,
}) {
    const reduced = useReducedMotion();

    if (reduced) {
        return <span className={cn(typeClassName, restClassName, className)}>{text}</span>;
    }

    return (
        <span className={cn('relative block', className)}>
            <motion.span
                aria-hidden="true"
                className={cn(typeClassName, restClassName, 'transition-colors duration-500')}
                initial="hidden"
                animate="visible"
                variants={{
                    hidden: {},
                    visible: { transition: { delayChildren: delay, staggerChildren: stagger } },
                }}
            >
                <WipeWords text={text} animated />
            </motion.span>

            <span
                aria-hidden="true"
                className={cn(
                    typeClassName,
                    wipeClassName,
                    'pointer-events-none absolute inset-0',
                    '[clip-path:inset(0_100%_0_0)] transition-[clip-path] duration-700',
                    '[transition-timing-function:var(--ease-out-expo)]',
                    'group-hover/wipe:[clip-path:inset(0_0_0_0)]',
                )}
            >
                <WipeWords text={text} />
            </span>
        </span>
    );
}
