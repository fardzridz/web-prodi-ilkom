(function () {
    window.ListFilter = function (opts) {
        var s = document.getElementById(opts.searchId);
        var grid = document.getElementById(opts.gridId);
        if (!s || !grid) return;

        var pills = typeof opts.filterSelector === 'string' ? document.querySelectorAll(opts.filterSelector) : [];
        var cards = grid.querySelectorAll('[data-search]');
        var counter = document.getElementById(opts.counterTextId);
        var empty = document.getElementById(opts.emptyId);
        var label = opts.label || '';
        var matchCat = opts.matchCat || 'exact';

        var activePill = function () {
            return document.querySelector(opts.filterSelector + '[aria-pressed="true"]');
        };

        var update = function () {
            var term = s.value.toLowerCase().trim();
            var active = activePill();
            var activeCat = active ? active.dataset.filter : 'semua';
            var visible = 0;

            cards.forEach(function (c) {
                var txt = c.dataset.search.toLowerCase();
                var cat = c.dataset.category || '';

                var catOk;
                if (matchCat === 'exact') {
                    catOk = activeCat === 'semua' || cat === activeCat;
                } else {
                    catOk = activeCat === 'semua' || txt.indexOf(activeCat) !== -1;
                }

                var searchOk = txt.indexOf(term) !== -1;
                var show = catOk && searchOk;
                c.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (counter) counter.textContent = visible + ' ' + label + ' ditemukan';
            if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
        };

        s.addEventListener('input', update);

        pills.forEach(function (p) {
            p.addEventListener('click', function () {
                var current = activePill();
                if (current) current.setAttribute('aria-pressed', 'false');
                p.setAttribute('aria-pressed', 'true');
                update();
            });
        });

        if (opts.toggle && opts.toggle.kartuBtnId && opts.toggle.listBtnId) {
            var kartuBtn = document.getElementById(opts.toggle.kartuBtnId);
            var listBtn = document.getElementById(opts.toggle.listBtnId);

            kartuBtn.addEventListener('click', function () {
                grid.classList.remove('docs-list-mode');
                kartuBtn.setAttribute('aria-pressed', 'true');
                listBtn.setAttribute('aria-pressed', 'false');
            });

            listBtn.addEventListener('click', function () {
                grid.classList.add('docs-list-mode');
                listBtn.setAttribute('aria-pressed', 'true');
                kartuBtn.setAttribute('aria-pressed', 'false');
            });
        }

        update();
    };
})();
