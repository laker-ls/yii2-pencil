$(document.body).ready(function () {
    let gallery = new AjaxGallery();

    gallery.index();
});

/**
 * Класс реализует работу с изображениями (crud) в модальном окне.
 */
class AjaxGallery {

    /** Отображение формы для изображений в модальном окне */
    index() {
        let self = this;

        $('[data-modal="pencil-image"]').on("click", function (event) {
            event.preventDefault();
            let data = {
                "group": $(this).attr("data-group"),
                "width": $(this).attr("data-width"),
                "height": $(this).attr("data-height"),
            };

            $.ajax({url: "/pencil/image/index", type: "get", data: data}).done(
                (result) => {
                    let modal = $(result);

                    modal.modal("show");
                    self.sortable(modal);
                    self.preview(modal);
                    self.deleteImg(modal);
                    self.fileButton(modal);

                    modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                }
            );
        });
    }

    /** Сортировка изображений с помощью jquery UI. Позиции изображений сохраняются в базу данных. */
    sortable(modal) {
        let container = modal.find(".preview");

        container.sortable({"containment": "parent", "tolerance": "pointer", "scroll": false});
        this.submitForm(modal);
    }

    /** Добавление изображений к уже существующим. */
    preview(modal) {
        let self = this;
        let imageInput = modal.find("input[type='file']");
        let preview = modal.find(".preview");

        imageInput.on("change", function () {
            let files = $(this)[0].files;
            let error;

            preview.find(".pre-load").remove();
            for (let index = 0; index < files.length; index++) {
                let reader = new FileReader();
                let imagesCompare = preview.find(".cart");
                let classInform;

                classInform = self.validateName(files[index].name, imagesCompare);

                if (classInform === 'error') {
                    error = classInform;
                }

                reader.onload = function (event) {
                    preview.append(
                        '<div class="col-lg-3 cart pre-load ' + classInform + '">' +
                            '<img class="img-fluid" src="' + event.target.result + '"> ' +
                            '<p class="name-img">' + files[index].name + '</p>' +
                        '</div>'
                    );
                };
                reader.readAsDataURL(files[index]);

                let submitButton = modal.find("[type='submit']");
                if (index + 1 === files.length && error === 'error') {
                    let message = 'Совпадение имен изображений!';

                    modal.find(".action").append('<div class="error-label">' + message + '</div>');
                    submitButton.attr({"disabled": true});
                } else {
                    modal.find(".action").find("div").remove();
                    submitButton.attr({"disabled": false});
                }
            }
        })
    }

    /** Проверка на совпадение имен. */
    validateName(imageName, imagesCompare) {
        let compareResult = 'success';

        imagesCompare.each(function (key, img) {
            let name = $(img).find(".name-img").text();

            if (imageName === name) {
                compareResult = 'error';
                return false;
            }
        });

        return compareResult;
    }

    /**
     * Отправка формы. По нажатию на кнопку отправки создаются инпуты для сохранения позиции в базе данных.
     */
    submitForm(modal) {
        let self = this;
        let form = modal.find("form");

        form.on("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            let uploadImg = modal.find(".preview").find(".cart");

            uploadImg.each(function (key, img) { // Добавляем в массив данные о позиции каждого изображения.
                let nameImg = $(img).find(".name-img").text();

                formData.set("Position", nameImg);
                formData.set("Position[" + nameImg + "]", key + 1);
            });

            $.ajax({
                url: "/pencil/image/create-update",
                type: "post",
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
            }).done(
                (result) => {
                    let group = result[0].group;

                    self.refreshDisplayImg(result, group);
                    modal.modal("hide");
                }
            )
        });
    }

    /**
     * Удаление изображений по нажатию на значек удаления.
     * После удаления изображения изменения происходят только в "предпросмотре" но не в самом документе, поэтому
     * рекомендуется нажимать отправку формы, для обновления страницы.
     */
    deleteImg(modal) {
        let self = this;
        let del = modal.find(".preview").find(".delete").find("a");

        del.on("click", function (event) {
            event.preventDefault();
            let accept = confirm('Вы действительно хотите удалить изображение?');
            let parent = $(this).closest(".cart");
            let data = {"id": parent.attr("id"), "group": parent.attr("data-group")};
            let group = parent.attr("data-group");

            if(accept) {
                $.ajax({url: "/pencil/image/delete", type: "post", data: data, dataType: "json"}).done (
                    (result) => {
                        self.refreshDisplayImg(result, group);
                        parent.remove(); // удаление изображения из модального окна.
                    }
                );
            }
        });
    }

    /**
     * Отображение новых изображений, после загрузки/удаления изображений.
     * Все изображения текущей группы удаляются и загружаются вновь через ajax.
     */
    refreshDisplayImg(result, group) {
        let container = $("[data-target='example-" + group + "']");
        let button = $('[data-modal="pencil-image"][data-group="' + group + '"]');

        container.nextUntil('[data-modal="pencil-image"]').remove();

        $(result).each(function (key, img) { // берем шаблон из html, заполняем его и дублируем в нужное место.
            let templateImg = $('[data-target="example-' + img.group + '"]');
            let instanceTemplateImg = templateImg.html()
                .replace(/#{url-mini}/gi, img.mini)
                .replace(/#{url-full}/gi, img.full)
                .replace(/#{alt}/gi, img.alt)
                .replace(/#{group}/gi, img.group);

            container.after(instanceTemplateImg);
        });
    }

    /** Стилизация кнопки для выбора изображений. */
    fileButton(modal) {
        let defaultInput = modal.find(".default-input");
        let newInput = modal.find(".new-input");

        defaultInput.on("mouseover", function () {
            newInput.css({"background-color": "#218838"});
        });

        defaultInput.on("mouseout", function () {
            newInput.css({"background-color": "#28a745"});
        });
    }
}