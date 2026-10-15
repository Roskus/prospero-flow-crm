@once
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css">
<script>
    document.querySelectorAll('select[data-tom-select]').forEach(function (element) {
        new TomSelect(element, {
            maxOptions: null,
            plugins: element.required ? [] : {
                clear_button: {
                    title: @json(__('Clear')),
                },
            },
        });
    });
</script>
@endonce
