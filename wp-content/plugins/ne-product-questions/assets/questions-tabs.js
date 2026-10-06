document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash !== '#tab-questions') {
        return;
    }

    // WooCommerce initializes product tabs after DOMContentLoaded.
    setTimeout(() => {
        const tab = document.querySelector(
            '#tab-title-questions a'
        );

        if (!tab) {
            return;
        }

        tab.click();
    }, 100);
});