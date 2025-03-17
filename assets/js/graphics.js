// Инициализация canvas
const canvas = document.getElementById("geometryCanvas");
const ctx = canvas.getContext("2d");
const shapeSelect = document.getElementById("shapeSelect");
const shapeParams = document.getElementById("shapeParams");

// Хранение текущих настроек
let currentShape = {
  type: "triangle",
  fillColor: "#f0f0f0",
  strokeColor: "#000000",
  params: {},
};

// Обновление формы параметров
function updateParamsForm() {
  const shape = shapeSelect.value;
  currentShape.type = shape;
  let html = "";

  switch (shape) {
    case "triangle":
      html = `
                <input type="number" name="side1" placeholder="Сторона 1" required min="10" max="300">
                <input type="number" name="side2" placeholder="Сторона 2" required min="10" max="300">
                <input type="number" name="side3" placeholder="Сторона 3" required min="10" max="300">
            `;
      break;
    case "rectangle":
      html = `
                <div>
                    <label for="width">Ширина:</label>
                    <input type="number" id="width" name="width" value="${
                      currentShape.width || 100
                    }" required min="10" max="500">
                </div>
                <div>
                    <label for="height">Высота:</label>
                    <input type="number" id="height" name="height" value="${
                      currentShape.height || 50
                    }" required min="10" max="300">
                </div>
            `;
      break;
    case "circle":
      html = `
                <div>
                    <label for="radius">Радиус:</label>
                    <input type="number" id="radius" name="radius" value="${
                      currentShape.radius || 50
                    }" required min="10" max="150">
                </div>
            `;
      break;
  }

  document.getElementById("shapeParams").innerHTML = html;
}

// Обработчики событий
shapeSelect.addEventListener("change", updateParamsForm);
document.getElementById("fillColor").addEventListener("change", (e) => {
  currentShape.fillColor = e.target.value;
  drawShape(currentShape);
});
document.getElementById("strokeColor").addEventListener("change", (e) => {
  currentShape.strokeColor = e.target.value;
  drawShape(currentShape);
});

// Добавляем обработчики для параметров прямоугольника и круга
document.addEventListener("input", function (e) {
  if (
    e.target.matches('[name="width"], [name="height"]') &&
    currentShape.type === "rectangle"
  ) {
    currentShape.width =
      parseInt(document.querySelector('[name="width"]').value) || 100;
    currentShape.height =
      parseInt(document.querySelector('[name="height"]').value) || 50;
    drawShape(currentShape);
  } else if (
    e.target.matches('[name="radius"]') &&
    currentShape.type === "circle"
  ) {
    currentShape.radius = parseInt(e.target.value) || 50;
    drawShape(currentShape);
  }
});

// Обновляем обработчик изменения типа фигуры
shapeSelect.addEventListener("change", function () {
  const shape = this.value;
  currentShape.type = shape;
  currentShape.points = []; // Очищаем точки при смене фигуры
  updateParamsForm();

  // Устанавливаем начальные размеры
  if (shape === "rectangle") {
    currentShape.width = 100;
    currentShape.height = 50;
  } else if (shape === "circle") {
    currentShape.radius = 50;
  }

  // Перерисовываем фигуру
  drawShape(currentShape);
});

// Функция отрисовки фигур
function drawShape(params) {
  // Сохраняем текущие стили
  const prevStrokeStyle = ctx.strokeStyle;
  const prevFillStyle = ctx.fillStyle;
  
  // Очищаем холст
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  
  if (!params) return;
  
  // Устанавливаем новые стили
  ctx.strokeStyle = params.strokeColor || "#000";
  ctx.fillStyle = params.fillColor || "#f0f0f0";
  
  switch (params.type) {
      case "triangle":
          if (params.points && params.points.length === 3) {
              drawTriangle(params);
          }
          break;
          
      case "rectangle":
          drawRectangle(params);
          break;
          
      case "circle":
          drawCircle(params);
          break;
  }
  
  // Восстанавливаем предыдущие стили
  ctx.strokeStyle = prevStrokeStyle;
  ctx.fillStyle = prevFillStyle;
}

// Устанавливаем начальные параметры при загрузке страницы
window.addEventListener('load', function() {
  // Устанавливаем начальные значения
  currentShape = {
      type: "rectangle",
      width: 100,
      height: 50,
      fillColor: "#f0f0f0",
      strokeColor: "#000000"
  };
  
  // Обновляем форму параметров
  shapeSelect.value = currentShape.type;
  updateParamsForm();
  
  // Отрисовываем начальную фигуру
  drawShape(currentShape);
});

function drawTriangle(params) {
  const points = params.points || [];
  if (points.length === 3) {
    const triangle = new Triangle(
      new Point(points[0].x, points[0].y),
      new Point(points[1].x, points[1].y),
      new Point(points[2].x, points[2].y)
    );
    triangle.draw(ctx);
    updateCalculations(triangle);
    currentShape.calculations = {
      sides: triangle.sides,
      perimeter: triangle.perimeter,
      area: triangle.area,
      innerRadius: triangle.innerRadius,
      outerRadius: triangle.outerRadius,
      centroid: triangle.centroid,
      innerCenter: triangle.innerCenter,
      outerCenter: triangle.outerCenter,
    };
  }
}

function drawRectangle(params) {
  const centerX = canvas.width / 2;
  const centerY = canvas.height / 2;
  const x = centerX - params.width / 2;
  const y = centerY - params.height / 2;

  const rectangle = new Rectangle(
    x,
    y,
    parseInt(params.width),
    parseInt(params.height)
  );
  rectangle.draw(ctx);
  updateCalculations(rectangle);
  currentShape.calculations = {
    perimeter: rectangle.perimeter,
    area: rectangle.area,
    diagonal: rectangle.diagonal,
    center: rectangle.center,
  };
}

function drawCircle(params) {
  const circle = new Circle(
    canvas.width / 2,
    canvas.height / 2,
    parseInt(params.radius)
  );
  circle.draw(ctx);
  updateCalculations(circle);
  currentShape.calculations = {
    diameter: circle.diameter,
    circumference: circle.circumference,
    area: circle.area,
    center: circle.center,
  };
}

function updateCalculations(shape) {
  const calculationsDiv = document.getElementById("calculations");
  let html = "";

  if (shape instanceof Triangle) {
    html = `
            <h3>Вычисления для треугольника:</h3>
            <p>Длины сторон: ${shape.sides
              .map((s) => s.toFixed(2))
              .join(", ")}</p>
            <p>Периметр: ${shape.perimeter.toFixed(2)}</p>
            <p>Площадь: ${shape.area.toFixed(2)}</p>
            <p>Радиус вписанной окружности: ${shape.innerRadius.toFixed(2)}</p>
            <p>Радиус описанной окружности: ${shape.outerRadius.toFixed(2)}</p>
            <p>Центр тяжести: (${shape.centroid.x.toFixed(
              2
            )}, ${shape.centroid.y.toFixed(2)})</p>
        `;
  } else if (shape instanceof Rectangle) {
    html = `
            <h3>Вычисления для прямоугольника:</h3>
            <p>Ширина: ${shape.width}</p>
            <p>Высота: ${shape.height}</p>
            <p>Периметр: ${shape.perimeter.toFixed(2)}</p>
            <p>Площадь: ${shape.area.toFixed(2)}</p>
            <p>Длина диагонали: ${shape.diagonal.toFixed(2)}</p>
            <p>Центр: (${shape.center.x.toFixed(2)}, ${shape.center.y.toFixed(
      2
    )})</p>
        `;
  } else if (shape instanceof Circle) {
    html = `
            <h3>Вычисления для круга:</h3>
            <p>Радиус: ${shape.radius}</p>
            <p>Диаметр: ${shape.diameter.toFixed(2)}</p>
            <p>Длина окружности: ${shape.circumference.toFixed(2)}</p>
            <p>Площадь: ${shape.area.toFixed(2)}</p>
            <p>Центр: (${shape.center.x.toFixed(2)}, ${shape.center.y.toFixed(
      2
    )})</p>
        `;
  }
  calculationsDiv.innerHTML = html;
}

// Обработка формы
document
  .getElementById("graphicsForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const title = formData.get("title");
    const shape = formData.get("shape");

    let params = {
      title: title,
      type: shape,
      fillColor: formData.get("fillColor"),
      strokeColor: formData.get("strokeColor"),
    };

    // Добавляем специфические параметры для каждой фигуры
    switch (shape) {
      case "rectangle":
        params.width = parseInt(formData.get("width")) || 100;
        params.height = parseInt(formData.get("height")) || 50;
        break;

      case "circle":
        params.radius = parseInt(formData.get("radius")) || 50;
        break;

      case "triangle":
        params.points = currentShape.points || [];
        break;
    }

    // Обновляем текущую фигуру
    currentShape = params;

    // Отрисовываем фигуру
    drawShape(currentShape);

    // Подготавливаем данные для отправки
    formData.append("vertices", JSON.stringify(currentShape.vertices || []));
    formData.append(
      "calculations",
      JSON.stringify(currentShape.calculations || {})
    );
    formData.append("params", JSON.stringify(currentShape));

    // Отправляем данные
    fetch("/vendor/components/graphics.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Фигура успешно сохранена!");
          location.reload();
        } else {
          alert(data.error || "Ошибка сохранения");
        }
      })
      .catch((error) => console.error("Ошибка:", error));
  });

// Загрузка сохраненных работ
document.querySelectorAll(".load-graphic").forEach((button) => {
  button.addEventListener("click", function () {
    const params = JSON.parse(this.dataset.params);
    currentShape = { ...params };

    // Обновляем значения в форме
    document.querySelector('[name="title"]').value = params.title || "";
    document.querySelector("#fillColor").value = params.fillColor || "#f0f0f0";
    document.querySelector("#strokeColor").value =
      params.strokeColor || "#000000";
    shapeSelect.value = params.type;
    updateParamsForm();

    // Добавляем параметры для прямоугольника и круга в форму
    if (params.type === "rectangle") {
      document.querySelector('[name="width"]').value = params.width || 100;
      document.querySelector('[name="height"]').value = params.height || 50;
    } else if (params.type === "circle") {
      document.querySelector('[name="radius"]').value = params.radius || 50;
    }

    drawShape(currentShape);
  });
});

// Обработчик клика по canvas
canvas.addEventListener("click", function (e) {
  const rect = canvas.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  // Обработка только для треугольника
  if (currentShape.type === "triangle") {
    if (!currentShape.points) {
      currentShape.points = [];
    }

    currentShape.points.push(new Point(x, y));

    // Отображаем точки при построении
    ctx.beginPath();
    ctx.arc(x, y, 3, 0, Math.PI * 2);
    ctx.fillStyle = "red";
    ctx.fill();

    if (currentShape.points.length > 1) {
      ctx.beginPath();
      ctx.moveTo(
        currentShape.points[currentShape.points.length - 2].x,
        currentShape.points[currentShape.points.length - 2].y
      );
      ctx.lineTo(x, y);
      ctx.strokeStyle = currentShape.strokeColor;
      ctx.stroke();
    }

    if (currentShape.points.length === 3) {
      drawShape(currentShape);
      currentShape.vertices = currentShape.points;
      currentShape.points = [];
    }
  }
});

// Инициализация
updateParamsForm();
