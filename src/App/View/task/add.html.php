<?php
use App\Core\Html\Context;

/** @var Context $context */
?>

<div class="row"><h1><?=$context->getHeading()?></h1></div>

<div class="row">
    <div class="col-xs-6 m-4">
        <form action="/insert_task/" method="post">
            <div class="form-label-group m-2">
                <label for="userName" class="sr-only">Имя пользователя:</label>
                <input type="text" class="form-control" id="userName" name="user_name" value="" placeholder="Имя пользователя" maxlength="512" required="required" />
            </div>

            <div class="form-label-group m-2">
                <label for="email" class="sr-only">Email:</label>
                <input type="text" class="form-control" id="email" name="email" value="" placeholder="Email" maxlength="512" required="required" />
            </div>

            <div class="form-label-group m-2">
                <label for="text" class="sr-only">Текст:</label>
                <textarea id="text" class="form-control" name="text" rows="5" placeholder="Текст" maxlength="1024" required="required"></textarea>
            </div>

            <div class="form-label-group m-2">
                <label for="file">Изображение:</label>
                <input type="file" class="form-control" id="file" name="file" placeholder="Изображение" />
            </div>

            <div id="imagePreviewContainer"></div>

            <input type="hidden" id="imageId" name="image_id" value="0" />

            <div class="form-label-group m-2">
                <button type="button" class="btn btn-light" id="btnPreview">Предпросмотр</button>
                <button type="submit" class="btn btn-primary">Создать</button>
                <a class="btn btn-secondary" href="/">Отмена</a>
                <button type="button" class="btn btn-warning" id="btnLoremIpsum">Lorem Ipsum!</button>
            </div>
        </form>
    </div>
    <div class="col-xs-6 m-4" id="previewContainer"></div>
</div>
