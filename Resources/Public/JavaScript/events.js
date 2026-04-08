;(function () {
    const eventsContainer = document.querySelectorAll(".events");

    if(eventsContainer) {
        eventsContainer.forEach(function(container) {
            const readMoreTitle = container.dataset.readmoreTitle;
            const eventsListsItems = container.querySelectorAll("article");

            eventsListsItems.forEach((addressListItem) => {
                const link = addressListItem.querySelector("a");
                if(link) {
                    addressListItem.classList.add("cursor-pointer");
                    addressListItem.addEventListener("click", function(event) {
                        openLink(link.getAttribute("href"));
                    });

                    if(readMoreTitle) {
                        let a = document.createElement('a');
                        let readMoreLink = document.createTextNode(readMoreTitle);
                        a.appendChild(readMoreLink);
                        a.href = link.getAttribute("href");
                        a.classList.add("read-more");
                        addressListItem.appendChild(a);
                    }
                }
            });

            function openLink(link, target) {
                if(target === "_blank") {
                    window.open(link);
                } else {
                    window.location.href = link;
                }
            }
        })
    }
})();
