document.addEventListener('DOMContentLoaded', function() {
    const categoryItems = document.querySelectorAll('.category-item');
    const products = document.querySelectorAll('.product');

    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            filterProducts(category);
            updateBreadcrumb(category);
        });
    });

    function filterProducts(category) {
        products.forEach(product => {
            if (category === 'All' || product.getAttribute('data-category') === category) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }

    function updateBreadcrumb(category) {
        const breadcrumbDiv = document.getElementById('breadcrumb');
        breadcrumbDiv.textContent = ` > ${category}`;
    }

});