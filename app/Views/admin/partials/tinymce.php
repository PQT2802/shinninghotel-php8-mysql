<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.rich-editor',
            license_key: 'gpl',
            base_url: '<?= asset('vendor/tinymce') ?>',
            suffix: '.min',
            height: 400,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_style: 'body { font-family: "Be Vietnam Pro", sans-serif; font-size: 14px; }'
        });
    }
});
</script>
