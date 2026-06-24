// Command to generate minified JS:
// php bin/minify js/krumo.js js/krumo.min.js

'use strict';

/**
 * JavaScript routines for Krumo
 */

/////////////////////////////////////////////////////////////////////////////

const krumo = {};

krumo.reclass = (el, className) => {
    el.classList.add(className);
};

krumo.unclass = (el, className) => {
    el.classList.remove(className);
};

krumo.toggle = (event) => {
    const elem = event.target.closest('.krumo-expand');
    const ctrl = event.ctrlKey;

    if (elem === null) {
        event.stopPropagation();
        return;
    }

    const isExpanded = !elem.classList.contains('krumo-opened');

    if (ctrl) {
        if (isExpanded) {
            krumo.expand_all(elem);
        } else {
            krumo.collapse_all(elem);
        }
    } else {
        if (isExpanded) {
            krumo.expand(elem);
        } else {
            krumo.collapse(elem);
        }
    }

    event.stopPropagation();
};

krumo.expand = (el) => {
    const paren = el.parentNode;
    const nest = paren.querySelector('.krumo-nest');
    const exp = paren.querySelector('.krumo-expand');

    nest.style.display = 'block';
    krumo.reclass(exp, 'krumo-opened');
};

krumo.collapse = (el) => {
    const paren = el.parentNode;
    const nest = paren.querySelector('.krumo-nest');
    const exp = paren.querySelector('.krumo-expand');

    nest.style.display = 'none';
    krumo.unclass(exp, 'krumo-opened');
};

krumo.expand_all = (el) => {
    const paren = el.parentNode;
    const nests = paren.querySelectorAll('.krumo-nest');
    const exps = paren.querySelectorAll('.krumo-expand');

    nests.forEach((item) => {
        item.style.display = 'block';
    });

    exps.forEach((item) => {
        krumo.reclass(item, 'krumo-opened');
    });
};

krumo.collapse_all = (el) => {
    const paren = el.parentNode;
    const nests = paren.querySelectorAll('.krumo-nest');
    const exps = paren.querySelectorAll('.krumo-expand');

    nests.forEach((item) => {
        item.style.display = 'none';
    });

    exps.forEach((item) => {
        krumo.unclass(item, 'krumo-opened');
    });
};

krumo.over = (el) => {
    krumo.reclass(el, 'krumo-hover');
};

krumo.out = (el) => {
    krumo.unclass(el, 'krumo-hover');
};

/////////////////////////////////////////////////////////////////////////////

const qsa = (search) => document.querySelectorAll(search);

const init_click = () => {
    const elems = qsa('.krumo-expand');

    elems.forEach((el) => {
        el.parentNode.addEventListener('click', krumo.toggle);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    init_click();
});
