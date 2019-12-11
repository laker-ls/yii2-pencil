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
            
            let data = $(this).data();
            
            $.ajax({url: "/pencil/image/index", type: "get", data: data}).done(
                (result) => {
                    let modal = $(result);

                    modal.modal("show");
                    self.sortable(modal);
                    self.preview(modal);
                    self.deleteImg(modal);
                    self.deleteImgAll(modal);
                    self.fileButton(modal);
                    self.submitForm(data, modal);

                    modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                }
            );
        });
    }

    /**
     * Отправка формы. По нажатию на кнопку отправки создаются инпуты для сохранения позиции в базе данных.
     *
     * @param dataButton object кнопка по нажатию на которое, открывается модальное окно с необходимыми data-параметрами.
     * @param modal object модальное окно.
     */
    submitForm(dataButton, modal) {
        let self = this,
            form = modal.find("form");

        form.on("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this),
                uploadImg = modal.find(".preview").find(".cart");

            uploadImg.each(function (key, img) { // Добавляем в массив данные о позиции каждого изображения.
                let nameImg = $(img).find(".name-img").text();

                formData.set("Position", nameImg);
                formData.set("Position[" + nameImg + "]", key + 1);
            });

            for (let key in dataButton) {
                if (dataButton.hasOwnProperty(key)) {
                    formData.set("Image[" + key + "]", dataButton[key]);
                }
            }

            $.ajax({
                url: "/pencil/image/create-update",
                type: "post",
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
            }).done(
                (result) => {
                    if (result[0] !== undefined) {
                        let group = result[0].group;

                        self.refreshDisplayImg(result, group);
                    }
                    modal.modal("hide");
                }
            )
        });
    }

    /** Сортировка изображений с помощью jquery UI. Позиции изображений сохраняются в базу данных. */
    sortable(modal) {
        let container = modal.find(".preview");

        container.sortable({"containment": "parent", "tolerance": "pointer", "scroll": false});
    }

    /** Добавление изображений к уже существующим. */
    preview(modal) {
        let self = this,
            imageInput = modal.find("input[type='file']"),
            preview = modal.find(".preview");

        imageInput.on("change", function () {
            let files = $(this)[0].files,
                error;

            preview.find(".pre-load").remove();
            for (let index = 0; index < files.length; index++) {
                let reader = new FileReader(),
                    imagesCompare = preview.find(".cart"),
                    classInform;

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

                let submitButton = modal.find("[type='submit']"),
                    errorLabel = $("#modal-pencil-image").find(".error-label");

                if (index + 1 === files.length && error === 'error') {
                    let message = 'Совпадение имен изображений!';

                    modal.find(".error-label").append(message);
                    submitButton.attr({"disabled": true});
                    errorLabel.css({display: "block"});
                } else {
                    modal.find(".action").find("div").remove();
                    submitButton.attr({"disabled": false});
                    errorLabel.css({display: "none"});
                }
            }
        })
    }

    /** Проверка на совпадение имен. */
    validateName(imageName, imagesCompare) {
        let compareResult = "success";

        imagesCompare.each(function (key, img) {
            let name = $(img).find(".name-img").text();

            if (imageName === name) {
                compareResult = "error";
                return false;
            }
        });

        return compareResult;
    }

    /** Удаление изображений по нажатию на значек удаления. */
    deleteImg(modal) {
        let self = this,
            del = modal.find(".preview").find(".delete").find("a");

        del.on("click", function (event) {
            event.preventDefault();
            let accept = confirm('Вы действительно хотите удалить изображение?'),
                parent = $(this).closest(".cart"),
                data = {"id": parent.attr("id"), "group": parent.attr("data-group")},
                group = parent.attr("data-group");

            if(accept) {
                $.ajax({url: "/pencil/image/delete", type: "post", data: data, dataType: "json"}).done (
                    (result) => {
                        self.refreshDisplayImg(result, group);
                        parent.remove(); // удаление изображения из модального окна.

                        // Собственное событие, в случае удаления изображения.
                        let form = modal.find("form");

                        form.trigger("img-delete");

                        let submitButton = modal.find("[type='submit']"),
                            errorLabel = $("#modal-pencil-image").find(".error-label"),
                            removedImgName = parent.find(".name-img").text(),
                            matchingImage = $(".preview").find(".name-img:contains(" + removedImgName + ")"),
                            matchingParent = matchingImage.closest(".cart"),
                            classInform = self.validateName(removedImgName, matchingImage);

                        matchingParent.attr({class: "col-lg-3 cart pre-load " + classInform});

                        let error = $("#modal-pencil-image").find(".cart").hasClass("error");

                        if (error === true) {
                            let message = 'Совпадение имен изображений!';

                            modal.find(".error-label").append(message);
                            submitButton.attr({"disabled": true});
                            errorLabel.css({display: "block"});
                        } else {
                            modal.find(".action").find("div").remove();
                            submitButton.attr({"disabled": false});
                            errorLabel.css({display: "none"});
                        }
                    }
                );
            }
        });
    }

    /** Удаление всех изображений в группе. */
    deleteImgAll(modal) {
        let self = this,
            form = modal.find("form"),
            carts = modal.find(".cart"),
            group = modal.find("[data-group]").data("group"),
            deleteAll = modal.find(".delete-all");

        if (group === undefined) {
            deleteAll.attr({disabled: true});
        }

        form.on("img-delete", function () {
            let issetImg = modal.find(".cart").length;

            if (issetImg) {
                deleteAll.attr({disabled: false});
            } else {
                deleteAll.attr({disabled: true});
            }
        });

        deleteAll.on("click", function () {
            if (confirm('Вы действительно хотите удалить все изображения?')) {
                $.ajax({url: "/pencil/image/delete-all", type: "post", data: {group: group}, dataType: "json"}).done(
                    (result) => {
                        if (result) {
                            carts.remove();
                            self.refreshDisplayImg(null, group);
                            $(this).attr({disabled: true});
                        }
                    }
                )
            }
        });
    }

    /**
     * Отображение новых изображений, после загрузки/удаления изображений.
     * Все изображения текущей группы удаляются и загружаются вновь через ajax.
     */
    refreshDisplayImg(result, group) {
        let container = $("[data-target='example-" + group + "']");

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
        let defaultInput = modal.find(".default-input"),
            newInput = modal.find(".new-input");

        defaultInput.on("mouseover", function () {
            newInput.css({"background-color": "#218838"});
        });

        defaultInput.on("mouseout", function () {
            newInput.css({"background-color": "#28a745"});
        });
    }
}