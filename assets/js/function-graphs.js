class FunctionGraph {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.xScale = 30; // пикселей на единицу
        this.yScale = 30;
        this.xOffset = canvas.width / 2;
        this.yOffset = canvas.height / 2;
    }

    drawGrid() {
        const w = this.canvas.width;
        const h = this.canvas.height;
        
        this.ctx.strokeStyle = '#ddd';
        this.ctx.lineWidth = 1;

        // Вертикальные линии
        for(let x = 0; x <= w; x += this.xScale) {
            this.ctx.beginPath();
            this.ctx.moveTo(x, 0);
            this.ctx.lineTo(x, h);
            this.ctx.stroke();
        }

        // Горизонтальные линии
        for(let y = 0; y <= h; y += this.yScale) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, y);
            this.ctx.lineTo(w, y);
            this.ctx.stroke();
        }

        // Оси координат
        this.ctx.strokeStyle = '#000';
        this.ctx.lineWidth = 2;
        
        // Ось X
        this.ctx.beginPath();
        this.ctx.moveTo(0, this.yOffset);
        this.ctx.lineTo(w, this.yOffset);
        this.ctx.stroke();
        
        // Ось Y
        this.ctx.beginPath();
        this.ctx.moveTo(this.xOffset, 0);
        this.ctx.lineTo(this.xOffset, h);
        this.ctx.stroke();
    }

    plotFunction(type, params, color = '#3498db') {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.drawGrid();
        
        this.ctx.strokeStyle = color;
        this.ctx.lineWidth = 2;
        this.ctx.beginPath();

        const step = 1; // Шаг для построения
        let isFirstPoint = true;

        for(let px = 0; px <= this.canvas.width; px += step) {
            const x = (px - this.xOffset) / this.xScale;
            let y;

            switch(type) {
                case 'linear':
                    y = params.k * x + params.b;
                    break;
                case 'quadratic':
                    y = params.a * Math.pow(x, 2) + params.b * x + params.c;
                    break;
                case 'cubic':
                    y = params.a * Math.pow(x, 3) + params.b * Math.pow(x, 2) + params.c * x + params.d;
                    break;
                case 'exponential':
                    y = Math.pow(params.a, x);
                    break;
            }

            // Проверяем на выход за пределы
            if (y > 1000 || y < -1000 || !isFinite(y)) {
                isFirstPoint = true;
                continue;
            }

            const py = this.yOffset - y * this.yScale;

            if (isFirstPoint) {
                this.ctx.moveTo(px, py);
                isFirstPoint = false;
            } else {
                this.ctx.lineTo(px, py);
            }
        }
        
        this.ctx.stroke();
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('functionCanvas');
    const graph = new FunctionGraph(canvas);
    
    const functionSelect = document.getElementById('functionSelect');
    const paramsDiv = document.getElementById('functionParams');
    
    function updateGraph() {
        const type = functionSelect.value;
        const color = document.getElementById('graphColor').value;
        const params = {};
        
        document.querySelectorAll('#functionParams input').forEach(input => {
            params[input.name] = parseFloat(input.value) || 0;
        });
        
        graph.plotFunction(type, params, color);
    }
    
    functionSelect.addEventListener('change', function() {
        let html = '';
        
        switch(this.value) {
            case 'linear':
                html = `
                    <label>k: <input type="number" name="k" value="1" step="0.1"></label>
                    <label>b: <input type="number" name="b" value="0" step="0.1"></label>
                `;
                break;
            case 'quadratic':
                html = `
                    <label>a: <input type="number" name="a" value="1" step="0.1"></label>
                    <label>b: <input type="number" name="b" value="0" step="0.1"></label>
                    <label>c: <input type="number" name="c" value="0" step="0.1"></label>
                `;
                break;
            case 'cubic':
                html = `
                    <label>a: <input type="number" name="a" value="1" step="0.1"></label>
                    <label>b: <input type="number" name="b" value="0" step="0.1"></label>
                    <label>c: <input type="number" name="c" value="0" step="0.1"></label>
                    <label>d: <input type="number" name="d" value="0" step="0.1"></label>
                `;
                break;
            case 'exponential':
                html = `
                    <label>a: <input type="number" name="a" value="2" step="0.1" min="0.1"></label>
                `;
                break;
        }
        
        paramsDiv.innerHTML = html;
        updateGraph();
    });
    
    paramsDiv.addEventListener('input', updateGraph);
    document.getElementById('graphColor').addEventListener('input', updateGraph);
    
    // Начальная инициализация
    functionSelect.dispatchEvent(new Event('change'));
    
    document.getElementById('graphicsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Добавляем текущие параметры функции
        const params = {};
        document.querySelectorAll('#functionParams input').forEach(input => {
            params[input.name] = parseFloat(input.value) || 0;
        });
        
        formData.append('params', JSON.stringify(params));
        
        // Отправляем данные
        fetch('/vendor/components/graphics.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('График успешно сохранен!');
                location.reload();
            } else {
                alert(data.error || 'Ошибка сохранения');
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });
});
