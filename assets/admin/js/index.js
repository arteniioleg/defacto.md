import 'bootstrap';
import { initElementSlugGenerators } from "./slug-generator";
import { initElementDatePickers } from "./datepicker";
import { initElementEditors } from "./editors";
import { initElementGoogleMaps } from "./google-map";
import "./log-diff"

document.body.addEventListener("app:initElement", ({ detail: element }) => {
    initElementDatePickers(element);
    initElementSlugGenerators(element);
    initElementEditors(element);
    initElementGoogleMaps(element);
});

jQuery(() => document.body.dispatchEvent(new CustomEvent('app:initElement', {detail: document.body})));