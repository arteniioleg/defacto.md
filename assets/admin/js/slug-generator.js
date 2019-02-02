// https://gist.github.com/mathewbyrne/1280286
export function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^ăîșțâ\w\-]+/gi, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

export function initSlugGenerator($sources, $slug) {
    const eventNamespace = '.auto-slug';

    let enabled = !$slug.val().length;

    $sources.off(eventNamespace);
    $slug.off(eventNamespace);

    $sources.on(['keyup', 'change'].map(event => event + eventNamespace).join(' '), () => {
        if (!enabled) {
            return;
        }

        let values = [];
        $sources.each((i, sourceInput) => {
            values.push($(sourceInput).val());
        });
        $slug.val(slugify(values.join(' ')));
    });

    $slug.on('change'+ eventNamespace, () => {
        enabled = !$slug.val().length;
    });
}

export function initElementSlugGenerators(element) {
    $(element).find('input[data-slug-from]:not(.initialized)').addClass('initialized').each((i, slugInput) => {
        const $slug = $(slugInput);

        const $sources = $(
            $slug.attr('data-slug-from')
                .split(',')
                .map(nameSelector => 'input[name="' + $.trim(nameSelector) + '"]')
                .join(',')
        );

        initSlugGenerator($sources, $slug);
    });
}