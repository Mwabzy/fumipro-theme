/**
 * FumiPro — Hero Slides admin (WordPress media library integration)
 */
(function ($) {
    'use strict';

    var $list   = $('#hero-slides-list');
    var nextIdx = $('.fh-slide-card').length;

    // ── Open media library ───────────────────────────────────────────────────
    $('#fh-add-slide').on('click', function () {
        var frame = wp.media({
            title:    'Select Hero Background Images',
            button:   { text: 'Add to carousel' },
            multiple: true,
            library:  { type: 'image' },
        });

        frame.on('select', function () {
            frame.state().get('selection').toJSON().forEach(function (att) {
                var thumb = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
                appendSlide(att.id, att.url, thumb);
            });
            renumber();
        });

        frame.open();
    });

    // ── Remove slide ─────────────────────────────────────────────────────────
    $list.on('click', '.fh-remove', function () {
        $(this).closest('.fh-slide-card').remove();
        reindex();
        renumber();
    });

    // ── Build a new slide card ───────────────────────────────────────────────
    function appendSlide(id, url, thumbUrl) {
        var i = nextIdx++;
        var card = $('<div class="fh-slide-card" data-index="' + i + '">');

        var thumb = $('<div class="fh-thumb">').css('background-image', 'url(' + thumbUrl + ')');
        thumb.append('<button type="button" class="fh-remove" title="Remove slide">&times;</button>');
        thumb.append('<span class="fh-slide-num"></span>');

        var fields = $('<div class="fh-fields">');
        fields.append('<input type="hidden" name="slides[' + i + '][id]"  value="' + id + '">');
        fields.append('<input type="hidden" name="slides[' + i + '][url]" value="' + url + '">');
        fields.append('<label>Headline <small>(leave blank for default)</small></label>');
        fields.append('<input type="text" name="slides[' + i + '][headline]" placeholder="Protecting Your Home &amp; Business…">');
        fields.append('<label>Subheadline <small>(leave blank for default)</small></label>');
        fields.append('<input type="text" name="slides[' + i + '][sub]" placeholder="Fast, effective, eco-friendly…">');

        card.append(thumb).append(fields);
        $list.append(card);
    }

    // ── Re-index input names after remove so PHP gets a clean 0-based array ─
    function reindex() {
        nextIdx = 0;
        $list.find('.fh-slide-card').each(function () {
            var i = nextIdx++;
            $(this).attr('data-index', i);
            $(this).find('input[name]').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/slides\[\d+\]/, 'slides[' + i + ']'));
            });
        });
    }

    // ── Update the visible slide numbers ─────────────────────────────────────
    function renumber() {
        $list.find('.fh-slide-num').each(function (i) {
            $(this).text(i + 1);
        });
    }

})(jQuery);
