$(document).ready(function () {
    let ajax = new AjaxPencil();

    ajax.index();
});

class AjaxPencil {

    /** Отображение формы и содержимого в модальном окне */
    index() {
        let self = this;

        $('[data-modal="pencil-text"]').on("click", function () {
            let data = {
                "id": $(this).attr("data-id"),
                "category_id": $(this).attr("data-category"),
            };

            $.ajax({url: "/pencil/text/index", type: "get", data: data}).done(
                (result) => {
                    let modal = $(result);

                    modal.modal("show");
                    self.record($(this), modal);

                    modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                }
            );
        });
    }

    /**
     * Запись модели и подмена текста в редактируемой записи.
     *
     * @param button тег с атрибутом data-modal="pencil", на который нажали.
     * @param modalPencil модальное окно, которое было вызвано после нажатия на 'button'.
     */
    record(button, modalPencil) {
        modalPencil.find("form").on("submit", function (event) {
            event.preventDefault();
            $.ajax({url: $(this).attr("action"), type: "post", dataType: "html", data: $(this).serialize()}).done(
                (result) => {
                    modalPencil.modal("hide");
                    button.html(result);

                    if (button.html() !== "") {
                        let lineBreak = button.html().replace(/\r\n|\r|\n/g, "<br />");
                        button.html(lineBreak);
                    } else {
                        button.html("Добавить текст");
                    }
                }
            );
        })
    }
}