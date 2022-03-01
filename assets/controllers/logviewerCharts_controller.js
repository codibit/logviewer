import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
        this.element.addEventListener('chartjs:connect', this._onConnect);
    }

    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side effects
        this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
        this.element.removeEventListener('chartjs:connect', this._onConnect);
    }

    _onPreConnect(event) {
        // The chart is not yet created
        console.log(event.detail.options); // You can access the chart options using the event details


        event.detail.options.elements.line = {
          // 'borderColor': 'rgb(255, 205, 86)',
            'borderColor': function(context) {
                const chart = context.chart;
                const {ctx, chartArea} = chart;

                if (!chartArea) {
                    // This case happens on initial chart load
                    return;
                }
                const chartWidth = chartArea.right - chartArea.left;
                const chartHeight = chartArea.bottom - chartArea.top;
                if (!gradient || width !== chartWidth || height !== chartHeight) {
                    // Create the gradient because this is either the first render
                    // or the size of the chart has changed
                    width = chartWidth;
                    height = chartHeight;
                    gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgb(255, 205, 86)');
                    gradient.addColorStop(0.5, 'rgb(255, 100, 86)');
                    gradient.addColorStop(1, 'rgb(255, 0, 86)');
                }
                //return getGradient(ctx, chartArea);
                return gradient;
            },
        }


    }

    _onConnect(event) {
    }
}