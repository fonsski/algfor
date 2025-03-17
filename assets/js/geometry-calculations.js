class Point {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }

    distanceTo(other) {
        return Math.sqrt(Math.pow(this.x - other.x, 2) + Math.pow(this.y - other.y, 2));
    }
}

class Triangle {
    constructor(p1, p2, p3) {
        this.vertices = [p1, p2, p3];
        this.calculateProperties();
    }

    calculateProperties() {
        // Стороны
        this.sides = [
            this.vertices[0].distanceTo(this.vertices[1]),
            this.vertices[1].distanceTo(this.vertices[2]),
            this.vertices[2].distanceTo(this.vertices[0])
        ];

        // Периметр
        this.perimeter = this.sides.reduce((a, b) => a + b, 0);

        // Полупериметр
        this.semiperimeter = this.perimeter / 2;

        // Площадь по формуле Герона
        this.area = Math.sqrt(
            this.semiperimeter *
            (this.semiperimeter - this.sides[0]) *
            (this.semiperimeter - this.sides[1]) *
            (this.semiperimeter - this.sides[2])
        );

        // Центр тяжести
        this.centroid = new Point(
            (this.vertices[0].x + this.vertices[1].x + this.vertices[2].x) / 3,
            (this.vertices[0].y + this.vertices[1].y + this.vertices[2].y) / 3
        );

        // Радиус вписанной окружности
        this.innerRadius = this.area / this.semiperimeter;

        // Радиус описанной окружности
        this.outerRadius = (this.sides[0] * this.sides[1] * this.sides[2]) / (4 * this.area);

        // Центр вписанной окружности
        const x = (this.sides[0] * this.vertices[0].x + this.sides[1] * this.vertices[1].x + this.sides[2] * this.vertices[2].x) / this.perimeter;
        const y = (this.sides[0] * this.vertices[0].y + this.sides[1] * this.vertices[1].y + this.sides[2] * this.vertices[2].y) / this.perimeter;
        this.innerCenter = new Point(x, y);

        // Центр описанной окружности
        const d = 2 * (this.vertices[0].x * (this.vertices[1].y - this.vertices[2].y) +
                      this.vertices[1].x * (this.vertices[2].y - this.vertices[0].y) +
                      this.vertices[2].x * (this.vertices[0].y - this.vertices[1].y));
        const xc = ((Math.pow(this.vertices[0].x, 2) + Math.pow(this.vertices[0].y, 2)) * (this.vertices[1].y - this.vertices[2].y) +
                   (Math.pow(this.vertices[1].x, 2) + Math.pow(this.vertices[1].y, 2)) * (this.vertices[2].y - this.vertices[0].y) +
                   (Math.pow(this.vertices[2].x, 2) + Math.pow(this.vertices[2].y, 2)) * (this.vertices[0].y - this.vertices[1].y)) / d;
        const yc = ((Math.pow(this.vertices[0].x, 2) + Math.pow(this.vertices[0].y, 2)) * (this.vertices[2].x - this.vertices[1].x) +
                   (Math.pow(this.vertices[1].x, 2) + Math.pow(this.vertices[1].y, 2)) * (this.vertices[0].x - this.vertices[2].x) +
                   (Math.pow(this.vertices[2].x, 2) + Math.pow(this.vertices[2].y, 2)) * (this.vertices[1].x - this.vertices[0].x)) / d;
        this.outerCenter = new Point(xc, yc);
    }

    draw(ctx) {
        // Рисуем треугольник
        ctx.beginPath();
        ctx.moveTo(this.vertices[0].x, this.vertices[0].y);
        ctx.lineTo(this.vertices[1].x, this.vertices[1].y);
        ctx.lineTo(this.vertices[2].x, this.vertices[2].y);
        ctx.closePath();
        ctx.stroke();
        ctx.fill();

        // Рисуем вписанную окружность
        ctx.beginPath();
        ctx.arc(this.innerCenter.x, this.innerCenter.y, this.innerRadius, 0, 2 * Math.PI);
        ctx.strokeStyle = 'blue';
        ctx.stroke();

        // Рисуем описанную окружность
        ctx.beginPath();
        ctx.arc(this.outerCenter.x, this.outerCenter.y, this.outerRadius, 0, 2 * Math.PI);
        ctx.strokeStyle = 'red';
        ctx.stroke();

        // Рисуем центр тяжести
        ctx.beginPath();
        ctx.arc(this.centroid.x, this.centroid.y, 3, 0, 2 * Math.PI);
        ctx.fillStyle = 'green';
        ctx.fill();
    }
}

class Rectangle {
    constructor(x, y, width, height) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.calculateProperties();
    }

    calculateProperties() {
        this.perimeter = 2 * (this.width + this.height);
        this.area = this.width * this.height;
        this.diagonal = Math.sqrt(Math.pow(this.width, 2) + Math.pow(this.height, 2));
        this.center = new Point(this.x + this.width/2, this.y + this.height/2);
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.rect(this.x, this.y, this.width, this.height);
        ctx.stroke();
        ctx.fill();

        // Рисуем центр
        ctx.beginPath();
        ctx.arc(this.center.x, this.center.y, 3, 0, 2 * Math.PI);
        ctx.fillStyle = 'green';
        ctx.fill();

        // Рисуем диагонали
        ctx.beginPath();
        ctx.moveTo(this.x, this.y);
        ctx.lineTo(this.x + this.width, this.y + this.height);
        ctx.moveTo(this.x + this.width, this.y);
        ctx.lineTo(this.x, this.y + this.height);
        ctx.strokeStyle = 'blue';
        ctx.stroke();
    }
}

class Circle {
    constructor(centerX, centerY, radius) {
        this.center = new Point(centerX, centerY);
        this.radius = radius;
        this.calculateProperties();
    }

    calculateProperties() {
        this.diameter = 2 * this.radius;
        this.circumference = Math.PI * this.diameter;
        this.area = Math.PI * Math.pow(this.radius, 2);
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.center.x, this.center.y, this.radius, 0, 2 * Math.PI);
        ctx.stroke();
        ctx.fill();

        // Рисуем центр
        ctx.beginPath();
        ctx.arc(this.center.x, this.center.y, 3, 0, 2 * Math.PI);
        ctx.fillStyle = 'green';
        ctx.fill();

        // Рисуем диаметр
        ctx.beginPath();
        ctx.moveTo(this.center.x - this.radius, this.center.y);
        ctx.lineTo(this.center.x + this.radius, this.center.y);
        ctx.moveTo(this.center.x, this.center.y - this.radius);
        ctx.lineTo(this.center.x, this.center.y + this.radius);
        ctx.strokeStyle = 'blue';
        ctx.stroke();
    }
}
