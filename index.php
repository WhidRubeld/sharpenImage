<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <title>ЦОИ - Лабораторная работа №1</title>
  </head>
  <body class="bg-light">
    <main class="container">
      <div class="d-flex justify-content-between align-items-center border-bottom py-2 mb-3">
        <div>
          <p class="lead mb-1">Цифрования обработка изображений</p>
          <p class="mb-0">Адаптивное увеличение резкости на основе локальной контрастности</p>
        </div>
        <div>
          <img src="images/guap.png" height="40px" />
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
              <button id="down" type="button" class="btn btn-primary" disabled="disabled">Назад</button>
              <button id="up" type="button" class="btn btn-primary">Вперед</button>
            </div>
            <small id="curr" class="text-muted">Изображение 1 из 4</small>
          </div>
          <div class="text-center">
            <img id="old" src="images/1.jpg" width="100%" />
          </div>
        </div>
        <div class="col-md-8 my-2">
          <div class="row align-items-center">
            <div class="col-md-4">
              <label for="radius">Радиус окрестности: 4</label>
              <input type="range" class="custom-range" min="1" max="9" value="4" id="radius">
            </div>
            <div class="col-md-4">
              <label for="n">Множитель: 0.6</label>
              <input type="range" class="custom-range" min="0" max="1" step="0.02" value="0.6" id="n">
            </div>
            <div class="col-md-4 text-right">
              <button id="submit" class="btn btn-primary">
                <div class="before">
                  Обработать изображение
                </div>
                <div class="after" style="display:none">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  Обработка
                </div>
              </button>
            </div>
          </div>
        </div>
        <div id="result" class="col-md-8 mt-1 mb-3" style="display: none">
          <div class="text-center">
            <p>Результат обработки</p>
            <img id="new" src="images/1.jpg" width="100%" />
          </div>
        </div>
      </div>

    </main>

    <script src="js/popper.min.js"></script>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>
