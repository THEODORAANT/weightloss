import re
import pathlib

BREAKPOINTS = {
    'sm': '(min-width: 640px)',
    'md': '(min-width: 768px)',
    'lg': '(min-width: 1024px)',
    'xl': '(min-width: 1280px)',
    '2xl': '(min-width: 1536px)',
}

PSEUDO_CLASSES = {
    'hover': ':hover',
    'focus': ':focus',
    'active': ':active',
}

GROUP_VARIANTS = {
    'group-hover': ':hover',
}

SPACING_SCALE = {
    '0': '0rem',
    '0.5': '0.125rem',
    '1': '0.25rem',
    '1.5': '0.375rem',
    '2': '0.5rem',
    '2.5': '0.625rem',
    '3': '0.75rem',
    '3.5': '0.875rem',
    '4': '1rem',
    '5': '1.25rem',
    '6': '1.5rem',
    '7': '1.75rem',
    '8': '2rem',
    '9': '2.25rem',
    '10': '2.5rem',
    '11': '2.75rem',
    '12': '3rem',
    '14': '3.5rem',
    '16': '4rem',
    '18': '4.5rem',
    '20': '5rem',
    '24': '6rem',
    '28': '7rem',
    '32': '8rem',
    '36': '9rem',
    '40': '10rem',
    '44': '11rem',
    '48': '12rem',
    '52': '13rem',
    '56': '14rem',
    '60': '15rem',
    '64': '16rem',
    '72': '18rem',
    '80': '20rem',
    '96': '24rem',
}

FONT_SIZES = {
    'xs': ('0.75rem', '1rem'),
    'sm': ('0.875rem', '1.25rem'),
    'base': ('1rem', '1.5rem'),
    'lg': ('1.125rem', '1.75rem'),
    'xl': ('1.25rem', '1.75rem'),
    '2xl': ('1.5rem', '2rem'),
    '3xl': ('1.875rem', '2.25rem'),
    '4xl': ('2.25rem', '2.5rem'),
    '5xl': ('3rem', '1'),
    '6xl': ('3.75rem', '1'),
}

FONT_WEIGHTS = {
    'thin': '100',
    'extralight': '200',
    'light': '300',
    'normal': '400',
    'medium': '500',
    'semibold': '600',
    'bold': '700',
    'extrabold': '800',
    'black': '900',
}

NAMED_COLORS = {
    'white': '#ffffff',
    'black': '#000000',
    'gray-50': '#f9fafb',
    'gray-100': '#f3f4f6',
    'gray-200': '#e5e7eb',
    'gray-300': '#d1d5db',
    'gray-400': '#9ca3af',
    'gray-500': '#6b7280',
    'gray-600': '#4b5563',
    'gray-700': '#374151',
    'gray-800': '#1f2937',
    'gray-900': '#111827',
    'blue-500': '#3b82f6',
    'blue-600': '#2563eb',
    'lime-500': '#84cc16',
}

BORDER_RADIUS = {
    'none': '0px',
    'sm': '0.125rem',
    '': '0.25rem',
    'md': '0.375rem',
    'lg': '0.5rem',
    'xl': '0.75rem',
    '2xl': '1rem',
    '3xl': '1.5rem',
    'full': '9999px',
}

SHADOWS = {
    'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
    '': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)',
    'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
    'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)',
    'xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
}

MAX_WIDTHS = {
    '7xl': '80rem',
}

Z_INDEX_MAP = {
    '0': '0',
    '10': '10',
    '20': '20',
    '30': '30',
    '40': '40',
    '50': '50',
}


def escape_class(name: str) -> str:
    escaped = ''.join((ch if ch.isalnum() or ch in ['-', '_'] else '\\' + ch) for ch in name)
    return '.' + escaped


def format_declarations(decls):
    return ''.join(f'{prop}:{value};' for prop, value in decls)


def spacing_value(token: str) -> str:
    if token in SPACING_SCALE:
        return SPACING_SCALE[token]
    return None


def generate_spacing(base: str):
    match = re.match(r'(m|p)([trblxy]?)\-\[([^\]]+)\]$', base)
    if match:
        kind, axis, raw = match.groups()
        value = raw
    else:
        match = re.match(r'(m|p)([trblxy]?)\-([\d.]+)$', base)
        if not match:
            return None
        kind, axis, raw = match.groups()
        value = spacing_value(raw)
        if value is None:
            return None
    props = []
    if axis == '':
        props = ['top', 'right', 'bottom', 'left']
    elif axis == 'x':
        props = ['left', 'right']
    elif axis == 'y':
        props = ['top', 'bottom']
    elif axis == 't':
        props = ['top']
    elif axis == 'r':
        props = ['right']
    elif axis == 'b':
        props = ['bottom']
    elif axis == 'l':
        props = ['left']
    else:
        return None
    property_name = 'margin' if kind == 'm' else 'padding'
    decls = [(f'{property_name}-{prop}', value) for prop in props]
    return decls


def generate_gap(base: str):
    match = re.match(r'gap\-\[([^\]]+)\]$', base)
    if match:
        return [('gap', match.group(1))]
    match = re.match(r'gap\-([\d.]+)$', base)
    if match:
        value = spacing_value(match.group(1))
        if value:
            return [('gap', value)]
    return None


def generate_size(base: str):
    if base in {'w-full', 'w-screen'}:
        return [('width', '100%')]
    if base == 'w-auto':
        return [('width', 'auto')]
    if base.startswith('w-'):
        match = re.match(r'w\-([\d]+)/([\d]+)$', base)
        if match:
            num, den = match.groups()
            return [('width', f'{float(num)/float(den)*100}%')]
    match = re.match(r'w\-\[([^\]]+)\]$', base)
    if match:
        return [('width', match.group(1))]
    if base == 'h-full':
        return [('height', '100%')]
    if base == 'h-auto':
        return [('height', 'auto')]
    match = re.match(r'h\-\[([^\]]+)\]$', base)
    if match:
        return [('height', match.group(1))]
    match = re.match(r'h\-([\d.]+)$', base)
    if match:
        value = spacing_value(match.group(1))
        if value:
            return [('height', value)]
    return None


def generate_font(base: str):
    if base.startswith('font-'):
        key = base.split('-', 1)[1]
        if key in FONT_WEIGHTS:
            return [('font-weight', FONT_WEIGHTS[key])]
    if base.startswith('text-'):
        key = base.split('-', 1)[1]
        if key in FONT_SIZES:
            size, line = FONT_SIZES[key]
            return [('font-size', size), ('line-height', line)]
        if key in NAMED_COLORS:
            return [('color', NAMED_COLORS[key])]
        if key.startswith('[') and key.endswith(']'):
            value = key[1:-1]
            if value.startswith('color:'):
                return [('color', value.split(':', 1)[1])]
            if re.match(r'^#|^rgb|^hsl|^var|^[a-zA-Z]+$', value):
                return [('color', value)]
            if re.match(r'^[-\d.]+(px|rem|em|%)$', value):
                return [('font-size', value)]
    if base.startswith('leading-'):
        key = base.split('-', 1)[1]
        if key == 'relaxed':
            return [('line-height', '1.625')]
        if key == 'tight':
            return [('line-height', '1.25')]
        if key.startswith('['):
            return [('line-height', key.strip('[]'))]
    if base.startswith('tracking-'):
        key = base.split('-', 1)[1]
        if key == 'tight':
            return [('letter-spacing', '-0.025em')]
        if key == 'wide':
            return [('letter-spacing', '0.05em')]
        if key.startswith('['):
            return [('letter-spacing', key.strip('[]'))]
    if base == 'uppercase':
        return [('text-transform', 'uppercase')]
    if base == 'lowercase':
        return [('text-transform', 'lowercase')]
    if base == 'capitalize':
        return [('text-transform', 'capitalize')]
    return None


def generate_grid(base: str):
    if base.startswith('grid-cols-'):
        value = base.split('-', 2)[2]
        if value.isdigit():
            count = int(value)
            if count > 0:
                return [('grid-template-columns', f'repeat({count}, minmax(0, 1fr))')]
    return None


def generate_background(base: str):
    if base.startswith('bg-['):
        return [('background-color', base[4:-1])]
    if base.startswith('bg-'):
        key = base[3:]
        if '/' in key:
            color, alpha = key.split('/')
            if color in NAMED_COLORS:
                hex_color = NAMED_COLORS[color]
                hex_color = hex_color.lstrip('#')
                if len(hex_color) == 6:
                    r = int(hex_color[0:2], 16)
                    g = int(hex_color[2:4], 16)
                    b = int(hex_color[4:6], 16)
                    alpha_value = int(alpha) / 100.0
                    return [('background-color', f'rgba({r},{g},{b},{alpha_value})')]
        if key in NAMED_COLORS:
            return [('background-color', NAMED_COLORS[key])]
        if key == 'white':
            return [('background-color', '#ffffff')]
        if key == 'black':
            return [('background-color', '#000000')]
        if key == 'light':
            return [('background-color', '#f8f9fa')]
        if key == 'secondary':
            return [('background-color', '#6c757d')]
        if key == 'info':
            return [('background-color', '#0dcaf0')]
    if base == 'bg-gradient-to-b':
        return [('background-image', 'linear-gradient(to bottom, var(--tw-gradient-from, transparent), var(--tw-gradient-to, transparent))')]
    if base.startswith('from-['):
        return [('--tw-gradient-from', base[6:-1]), ('--tw-gradient-stops', 'var(--tw-gradient-from), var(--tw-gradient-to, rgba(255,255,255,0))')]
    if base.startswith('to-['):
        return [('--tw-gradient-to', base[4:-1])]
    if base == 'bg-opacity-50':
        return [('background-color', 'rgba(0,0,0,0.5)')]
    return None


def generate_border(base: str):
    if base == 'border':
        return [('border-width', '1px'), ('border-style', 'solid'), ('border-color', 'currentColor')]
    if base == 'border-0':
        return [('border-width', '0')]
    if base == 'border-2':
        return [('border-width', '2px'), ('border-style', 'solid')]
    if base == 'border-b':
        return [('border-bottom-width', '1px'), ('border-style', 'solid'), ('border-color', 'currentColor')]
    if base == 'border-t':
        return [('border-top-width', '1px'), ('border-style', 'solid'), ('border-color', 'currentColor')]
    if base.startswith('border-['):
        return [('border-color', base[7:-1])]
    if base.startswith('border-[#'):
        return [('border-color', base[7:])]
    if base.startswith('border-gray-'):
        key = base.split('-', 2)[2]
        color = NAMED_COLORS.get(f'gray-{key}')
        if color:
            return [('border-color', color)]
    if base == 'border-white':
        return [('border-color', '#ffffff')]
    if base == 'border-black':
        return [('border-color', '#000000')]
    return None


def generate_radius(base: str):
    if base.startswith('rounded-['):
        return [('border-radius', base[8:-1])]
    if base == 'rounded':
        return [('border-radius', BORDER_RADIUS[''])]
    if base == 'rounded-full':
        return [('border-radius', BORDER_RADIUS['full'])]
    if base.startswith('rounded-'):
        key = base.split('-', 1)[1]
        value = BORDER_RADIUS.get(key)
        if value:
            return [('border-radius', value)]
    return None


def generate_shadow(base: str):
    if base.startswith('shadow-['):
        return [('box-shadow', base[8:-1])]
    if base == 'shadow':
        return [('box-shadow', SHADOWS[''])]
    if base.startswith('shadow-'):
        key = base.split('-', 1)[1]
        value = SHADOWS.get(key)
        if value:
            return [('box-shadow', value)]
    return None


def generate_position(base: str):
    if base in {'relative', 'absolute', 'fixed', 'sticky'}:
        return [('position', base)]
    if base.startswith('top-['):
        return [('top', base[5:-1])]
    if base == 'top-full':
        return [('top', '100%')]
    if base.startswith('bottom-['):
        return [('bottom', base[8:-1])]
    if base.startswith('left-['):
        return [('left', base[6:-1])]
    if base == 'left-auto':
        return [('left', 'auto')]
    if base.startswith('right-['):
        return [('right', base[7:-1])]
    if base == 'right-auto':
        return [('right', 'auto')]
    if base == 'top-0':
        return [('top', '0')]
    if base == 'bottom-0':
        return [('bottom', '0')]
    if base == 'bottom-auto':
        return [('bottom', 'auto')]
    if base == 'left-0':
        return [('left', '0')]
    if base == 'right-0':
        return [('right', '0')]
    if base == 'inset-0':
        return [('top', '0'), ('right', '0'), ('bottom', '0'), ('left', '0')]
    return None


def generate_flex(base: str):
    mapping = {
        'flex': [('display', 'flex')],
        'inline-flex': [('display', 'inline-flex')],
        'block': [('display', 'block')],
        'grid': [('display', 'grid')],
        'hidden': [('display', 'none')],
        'flex-col': [('flex-direction', 'column')],
        'flex-col-reverse': [('flex-direction', 'column-reverse')],
        'flex-row': [('flex-direction', 'row')],
        'flex-1': [('flex', '1 1 0%')],
        'flex-grow': [('flex-grow', '1')],
        'flex-grow-0': [('flex-grow', '0')],
        'flex-shrink-0': [('flex-shrink', '0')],
        'flex-wrap': [('flex-wrap', 'wrap')],
        'grid': [('display', 'grid')],
    }
    if base in mapping:
        return mapping[base]
    if base.startswith('items-'):
        align = base.split('-', 1)[1]
        values = {
            'start': 'flex-start',
            'end': 'flex-end',
            'center': 'center',
            'stretch': 'stretch',
            'baseline': 'baseline',
        }
        value = values.get(align)
        if value:
            return [('align-items', value)]
    if base.startswith('justify-'):
        justify = base.split('-', 1)[1]
        values = {
            'start': 'flex-start',
            'end': 'flex-end',
            'center': 'center',
            'between': 'space-between',
            'around': 'space-around',
            'evenly': 'space-evenly',
        }
        value = values.get(justify)
        if value:
            return [('justify-content', value)]
    if base.startswith('content-'):
        justify = base.split('-', 1)[1]
        values = {
            'center': 'center',
            'between': 'space-between',
        }
        value = values.get(justify)
        if value:
            return [('align-content', value)]
    if base.startswith('order-'):
        return [('order', base.split('-', 1)[1])]
    return None


def generate_misc(base: str):
    if base == 'mx-auto':
        return [('margin-left', 'auto'), ('margin-right', 'auto')]
    if base == 'my-auto':
        return [('margin-top', 'auto'), ('margin-bottom', 'auto')]
    if base == 'overflow-hidden':
        return [('overflow', 'hidden')]
    if base == 'overflow-clip':
        return [('overflow', 'clip')]
    if base == 'overflow-y-auto':
        return [('overflow-y', 'auto')]
    if base == 'overflow-x-auto':
        return [('overflow-x', 'auto')]
    if base == 'text-center':
        return [('text-align', 'center')]
    if base == 'text-left':
        return [('text-align', 'left')]
    if base == 'text-right':
        return [('text-align', 'right')]
    if base == 'text-justify':
        return [('text-align', 'justify')]
    if base == 'underline':
        return [('text-decoration', 'underline')]
    if base == 'no-underline':
        return [('text-decoration', 'none')]
    if base == 'min-h-screen':
        return [('min-height', '100vh')]
    if base.startswith('min-h-['):
        return [('min-height', base[8:-1])]
    if base.startswith('min-w-['):
        return [('min-width', base[8:-1])]
    if base.startswith('max-w-['):
        return [('max-width', base[8:-1])]
    if base.startswith('max-w-'):
        key = base.split('-', 2)[2]
        value = MAX_WIDTHS.get(key)
        if value:
            return [('max-width', value)]
    if base == 'max-w-none':
        return [('max-width', 'none')]
    if base.startswith('z-['):
        return [('z-index', base[3:-1])]
    if base.startswith('z-'):
        key = base.split('-', 1)[1]
        value = Z_INDEX_MAP.get(key)
        if value:
            return [('z-index', value)]
    if base == 'shadow-sm':
        return [('box-shadow', SHADOWS['sm'])]
    if base == 'shadow-md':
        return [('box-shadow', SHADOWS['md'])]
    if base == 'shadow-lg':
        return [('box-shadow', SHADOWS['lg'])]
    if base == 'shadow-xl':
        return [('box-shadow', SHADOWS['xl'])]
    if base == 'shadow':
        return [('box-shadow', SHADOWS[''])]
    if base == 'transition':
        return [('transition-property', 'all'), ('transition-duration', '150ms'), ('transition-timing-function', 'cubic-bezier(0.4, 0, 0.2, 1)')]
    if base == 'transition-all':
        return [('transition-property', 'all')]
    if base == 'transition-colors':
        return [('transition-property', 'color, background-color, border-color, text-decoration-color, fill, stroke')]
    if base == 'transition-opacity':
        return [('transition-property', 'opacity')]
    if base == 'transition-transform':
        return [('transition-property', 'transform')]
    if base.startswith('duration-'):
        return [('transition-duration', f"{int(base.split('-',1)[1])}ms")]
    if base.startswith('opacity-'):
        value = base.split('-', 1)[1]
        if value.isdigit():
            return [('opacity', str(int(value) / 100))]
        if value == '0':
            return [('opacity', '0')]
    if base == 'opacity-0':
        return [('opacity', '0')]
    if base == 'opacity-100':
        return [('opacity', '1')]
    if base.startswith('scale-'):
        amount = base.split('-', 1)[1]
        if amount.endswith(']') and amount.startswith('['):
            scale = amount[1:-1]
        else:
            try:
                scale = str(int(amount) / 100)
            except ValueError:
                scale = None
        if scale:
            return [('transform', f'scale({scale})')]
    if base == 'backdrop-blur-lg':
        return [('backdrop-filter', 'blur(16px)')]
    if base.startswith('object-['):
        return [('object-position', base[7:-1].replace('_', ' '))]
    if base == 'object-cover':
        return [('object-fit', 'cover')]
    if base == 'object-contain':
        return [('object-fit', 'contain')]
    if base == 'whitespace-nowrap':
        return [('white-space', 'nowrap')]
    if base == 'whitespace-normal':
        return [('white-space', 'normal')]
    if base == 'text-ellipsis':
        return [('text-overflow', 'ellipsis'), ('white-space', 'nowrap'), ('overflow', 'hidden')]
    if base == 'font-semibold':
        return [('font-weight', '600')]
    if base == 'font-medium':
        return [('font-weight', '500')]
    if base == 'font-normal':
        return [('font-weight', '400')]
    if base == 'font-bold':
        return [('font-weight', '700')]
    if base == 'leading-none':
        return [('line-height', '1')]
    if base == 'visible':
        return [('visibility', 'visible')]
    if base == 'invisible':
        return [('visibility', 'hidden')]
    if base == 'cursor-pointer':
        return [('cursor', 'pointer')]
    if base == 'list-disc':
        return [('list-style-type', 'disc')]
    if base == 'list-decimal':
        return [('list-style-type', 'decimal')]
    if base == 'list-inside':
        return [('list-style-position', 'inside')]
    if base == 'outline-none':
        return [('outline', '2px solid transparent'), ('outline-offset', '2px')]
    if base == 'ring-2':
        return [('--tw-ring-width', '2px'), ('box-shadow', '0 0 0 2px var(--tw-ring-color, rgba(59,130,246,0.5))')]
    if base == 'ring':
        return [('--tw-ring-width', '1px'), ('box-shadow', '0 0 0 1px var(--tw-ring-color, rgba(59,130,246,0.5))')]
    if base.startswith('ring-') and base not in {'ring', 'ring-2'}:
        key = base.split('-', 1)[1]
        color = NAMED_COLORS.get(key)
        if color:
            return [('--tw-ring-color', color)]
    return None


def generate_rule(base: str):
    generators = [
        generate_spacing,
        generate_gap,
        generate_size,
        generate_font,
        generate_grid,
        generate_background,
        generate_border,
        generate_radius,
        generate_shadow,
        generate_position,
        generate_flex,
        generate_misc,
    ]
    for gen in generators:
        decls = gen(base)
        if decls:
            return decls
    return None


def parse_classes(root: pathlib.Path):
    classes = set()
    for path in root.rglob('*.php'):
        text = path.read_text(encoding='utf-8', errors='ignore')
        for match in re.finditer(r'class\s*=\s*"([^"]*)"', text):
            classes.update(filter(None, re.split(r'\s+', match.group(1))))
        for match in re.finditer(r"class\s*=\s*'([^']*)'", text):
            classes.update(filter(None, re.split(r'\s+', match.group(1))))
    return classes


def build_css(classes):
    rules = {}
    skipped = []
    for cls in sorted(classes):
        parts = cls.split(':')
        base = parts[-1]
        variants = parts[:-1]
        if base.startswith('space-y-'):
            if base.startswith('space-y-['):
                gap_value = base[8:-1]
            else:
                value = spacing_value(base.split('-', 2)[2])
                gap_value = value
            if gap_value:
                selector = escape_class(base)
                child_selector = f"{selector}>:not([hidden])~:not([hidden])"
                rules.setdefault(tuple(), {})[child_selector] = f'margin-top:{gap_value};'
            continue
        decls = generate_rule(base)
        if not decls:
            continue
        selector = escape_class(base)
        pseudo = ''
        media_queries = []
        group_variants = []
        for variant in variants:
            if variant in PSEUDO_CLASSES:
                pseudo += PSEUDO_CLASSES[variant]
            elif variant in BREAKPOINTS:
                media_queries.append(variant)
            elif variant in GROUP_VARIANTS:
                group_variants.append(GROUP_VARIANTS[variant])
        full_selector = selector + pseudo
        css_line = format_declarations(decls)
        target_rules = rules.setdefault(tuple(media_queries), {}) if media_queries else rules.setdefault(tuple(), {})
        if group_variants:
            for group_state in group_variants:
                parent_selector = f".group{group_state} {full_selector}"
                target_rules[parent_selector] = css_line
        else:
            target_rules[full_selector] = css_line
    return rules


def render_css(rules):
    lines = []
    for medias, selectors in sorted(rules.items(), key=lambda item: ''.join(item[0])):
        block = []
        for selector, decls in sorted(selectors.items()):
            block.append(f"{selector}{{{decls}}}")
        css_block = ''.join(block)
        if medias:
            query = ' and '.join(f"({BREAKPOINTS[m]})" for m in medias)
            lines.append(f"@media {query}{{{css_block}}}")
        else:
            lines.append(css_block)
    return '\n'.join(lines)


def main():
    root = pathlib.Path(__file__).resolve().parent.parent / 'perch' / 'templates'
    classes = parse_classes(root)
    rules = build_css(classes)
    css = render_css(rules)
    output_path = pathlib.Path(__file__).resolve().parent.parent / 'public' / 'css' / 'tailwind-subset.css'
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(css)
    print(f"Wrote {output_path}")


if __name__ == '__main__':
    main()
