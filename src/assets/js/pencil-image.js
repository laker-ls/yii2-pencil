/**
 * Класс реализует работу с изображениями (crud) в модальном окне.
 */

$(document.body).ready(function () {
    let gallery = new AjaxGallery();

    gallery.index();
});

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
            let uploadIcon,
                formData = new FormData(this),
                uploadImg = modal.find(".preview").find(".cart"),
                buttonSubmit = $(this).find("button[type=submit]");

            uploadIcon = buttonSubmit.data('load-img');
            buttonSubmit.html(`<img class="loading-pencil" src="${uploadIcon}">`);

            uploadImg.each(function (key, img) { // Добавляем в массив данные о позиции каждого изображения.
                let nameImg = $(img).find(".name-img").data("full-name");

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
                    buttonSubmit.html("Сохранить");
                    if (result.status === "error") {
                        self.validateDisplay(modal, result.status, result.message);
                    } else if (result[0] !== undefined) {
                        let group = result[0].group;

                        self.refreshDisplayImg(result, group);
                        modal.modal("hide");
                    }
                }
            )
        });
    }

    /** Сортировка изображений с помощью jquery UI. Позиции изображений сохраняются в базу данных. */
    sortable(modal) {
        let self = this,
            container = modal.find(".preview"),
            validateResult = "success";

        container.sortable({
            "containment": "parent",
            "tolerance": "pointer",
            "scroll": false,

            "update": function (event, ui) {
                self.validateDisplay(modal, validateResult);
            }
        });
    }

    /** Добавление изображений к уже существующим. */
    preview(modal) {
        let self = this,
            imageInput = modal.find("input[type='file']"),
            preview = modal.find(".preview"),
            submitButton = modal.find("[type='submit']"),
            errorLabel = modal.find(".error-label");

        imageInput.on("change", function () {
            let files = $(this)[0].files,
                validateResult = "success";

            preview.find(".pre-load").remove();
            errorLabel.css({display: "none"});
            submitButton.attr({disabled: false});

            for (let index = 0; index < files.length; index++) {
                let reader = new FileReader(),
                    imagesCompare = preview.find(".cart"),
                    name = files[index].name.split(".")[0],
                    extension = files[index].name.split(".")[1],
                    classInform,
                    shortName,
                    fullName;

                if (name.length > 27) {
                    shortName = name.substring(0, 27) + extension;
                } else {
                    shortName = name + extension;
                }
                fullName = name + "." + extension;



                classInform = self.validateName(files[index].name, imagesCompare);
                if (classInform === "error") {
                    validateResult = "error";
                }

                reader.onload = function (event) {
                    preview.append(
                        `
                        <div class="col-lg-3 cart pre-load ${classInform}">
                            <div class="img-container">
                                <img src="${event.target.result}">
                            </div>
                            <p class="name-img" data-full-name="${fullName}">${shortName}</p>
                        </div>
                        `
                    );
                };
                reader.readAsDataURL(files[index]);
            }
            self.validateDisplay(modal, validateResult);
        });
    }

    /** Удаление изображений по нажатию на значек удаления. */
    deleteImg(modal) {
        let self = this,
            deleteButton = modal.find(".preview").find(".delete").find("a");

        deleteButton.on("click", function (event) {
            event.preventDefault();
            let accept = confirm('Вы действительно хотите удалить изображение?'),
                parent = $(this).closest(".cart"),
                data = {"id": parent.attr("id"), "group": parent.attr("data-group")},
                group = parent.attr("data-group");

            if(accept) {
                $.ajax({url: "/pencil/image/delete", type: "post", data: data, dataType: "json"}).done (
                    (result) => {
                        let form = modal.find("form"),
                            removedImgName = parent.find(".name-img").text(),
                            matchingImage = $(".preview").find(".name-img:contains(" + removedImgName + ")"),
                            matchingParent = matchingImage.closest(".cart"),
                            classInform = self.validateName(removedImgName, matchingImage),
                            cartError;

                        self.refreshDisplayImg(result, group);
                        parent.remove();
                        form.trigger("img-delete");

                        matchingParent.attr({class: "col-lg-3 cart pre-load " + classInform});

                        cartError = $("#modal-pencil-image").find(".cart.error");
                        if (cartError.length) {
                            classInform = 'error';
                        } else {
                            classInform = 'success';
                        }
                        self.validateDisplay(modal, classInform);
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
            if (confirm("Вы действительно хотите удалить все изображения?")) {
                $.ajax({url: "/pencil/image/delete-all", type: "post", data: {group: group}, dataType: "json"}).done(
                    (result) => {
                        let error = modal.find(".cart.error");

                        if (result) {
                            carts.remove();
                            self.refreshDisplayImg(null, group);
                            $(this).attr({disabled: true});

                            error.removeClass("error").addClass("success");
                            self.validateDisplay(modal, "success");
                        }
                    }
                )
            }
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

    /** Отображение ошибок валидации */
    validateDisplay(modal, compareResult, message) {
        let submitButton = modal.find("[type='submit']"),
            errorLabel = modal.find(".error-label");

        if (!message) {
            message = 'Совпадение имен изображений!';
        }

        if (compareResult === 'success') {
            modal.find(".action").find("div").remove();
            errorLabel.css({display: "none"});

            setTimeout(function () {
                if (modal.find(".cart.error").length === 0) {
                    submitButton.attr({"disabled": false});
                } else {
                    submitButton.attr({"disabled": true});
                }
            }, 10);
        } else if (compareResult === 'error') {
            modal.find(".error-label").html(message);
            submitButton.attr({"disabled": true});
            errorLabel.css({display: "block"});
        }
    }

    /**
     * Отображение новых изображений, после загрузки/удаления изображений.
     * Все изображения текущей группы удаляются и загружаются вновь через ajax.
     */
    refreshDisplayImg(result, group) {
        let container = $("[data-target='example-" + group + "']");

        container.nextUntil('.pencil-gallery').remove();

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
}