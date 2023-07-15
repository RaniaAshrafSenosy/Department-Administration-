import { Component, Input, OnInit, SimpleChanges } from '@angular/core';
import Chart from 'chart.js/auto';


@Component({
  selector: 'app-charts',
  templateUrl: './charts.component.html',
  styleUrls: ['./charts.component.css']
})
export class ChartsComponent implements OnInit {



@Input('data1') data1: any;
  @Input('data2') data2: any;
  @Input('label1') label1: any = 'Grades';
  @Input('label2') label2: any = 'Grades';
  data11=[1,2,3,4,5,6,7]
  public chart: any;

  constructor() {}

  ngOnInit(): void {
    this.createChart();
  }
  
  // updateChart() {
  //   this.chart.data.datasets[0].data = this.data1;
  //   this.chart.update();
  // }

  createChart() {
    // set the dataset array containing data1 and data2 if data1 and data2 are not null
    let arr = [];
    if (this.data1 && this.data2) {
      arr = [
        {
          label: this.label1,
          data: this.data1,
          backgroundColor: '#1d2c28',
        },
        {
          label: this.label2,
          data: this.data2,
          backgroundColor: '#6a6650',
        },
      ];
    } else {
      arr = [
        {
          label: this.label1,
          data: this.data11,
          backgroundColor: '#1d2c28',
        },
      ];
    }
    this.chart = new Chart('bar', {
      type: 'bar', //this denotes tha type of chart

      data: {
        // values on X-Axis
        labels: ['F', 'D', 'D+', 'C', 'C+', 'B', 'B+', 'A', 'A+'],
        datasets: arr,
      },
    });
  }

  // ngOnChanges(changes: SimpleChanges): void {
  //   if (changes['data1'] && !changes['data1'].firstChange) {
  //     this.updateChart();
  //   }
  // }

}
