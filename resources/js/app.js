import "./bootstrap";
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver/theme';
import 'tinymce/icons/default/icons';

import 'tinymce/plugins/code';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

import 'tinymce/skins/ui/oxide/skin.min.css';

document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
        selector: '#footer',
        height: 300,
        plugins: 'code link lists table wordcount',
        toolbar:
            'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link table | code',
        branding: false,
        license_key: 'gpl',

        // ✅ Important: fix model loading issue
        base_url: '/tinymce', // Adjusted to match where assets are served
        suffix: '.min',

        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    const form = document.querySelector('#createEmailTemplateForm');
    if (form) {
        form.addEventListener('submit', function () {
            tinymce.triggerSave();
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
        selector: '#header',
        height: 300,
        plugins: 'code link lists table wordcount',
        toolbar:
            'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link table | code',
        branding: false,
        license_key: 'gpl',

        // ✅ Important: fix model loading issue
        base_url: '/tinymce', // Adjusted to match where assets are served
        suffix: '.min',

        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    const form = document.querySelector('#createEmailTemplateForm');
    if (form) {
        form.addEventListener('submit', function () {
            tinymce.triggerSave();
        });
    }
});
