export default class Tabs {
  constructor() { this.init(); }

  init() {
    const activate = ($bar, idx) => {
      const $root  = $bar.parent();
      const $btns  = $bar.find('.tablinks');
      const $panes = $root.find('.tab-wrapper .tabcontent');

      // buttons
      $btns.removeClass('active')
           .attr({ 'aria-selected': 'false', 'aria-expanded': 'false' });
      $btns.eq(idx).addClass('active')
           .attr({ 'aria-selected': 'true', 'aria-expanded': 'true' });

      // panels (let your CSS animate .is-open)
      $panes.removeClass('is-open').prop('hidden', true)
            .eq(idx).addClass('is-open').prop('hidden', false);
    };

    // click handler (index-based within this .tab)
    $(document).on('click', '.tab .tablinks', function (e) {
      e.preventDefault();
      const $bar = $(this).closest('.tab');
      activate($bar, $bar.find('.tablinks').index(this));
    });

    // open first tab for each group on load
    $('.tab').each((_, el) => activate($(el), 0));
  }
}
