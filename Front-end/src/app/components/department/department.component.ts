import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';
import { DepartmentService } from 'src/app/services/department.service';
import { department } from 'src/app/interfaces/department';
import Chart from 'chart.js/auto';

// import { Chart } from 'angular-highcharts';
@Component({
  selector: 'app-department',
  templateUrl: './department.component.html',
  styleUrls: ['./department.component.css']
})
export class DepartmentComponent implements OnInit {
  deptid:number;
  deptname:string;
  deptcode:string
  department:department
  data11=[1,2,3,4,5,6,7]
  Slabel1=["number of secondments"]
  Llabel2=["number of legations"]
  secondmentYears:string[]=[];
  secondmentNumbers:number[]=[];
  legationYears:string[]=[];
  legationNumbers:number[]=[];
  bylaw: File;
  booklet: File;
  deptBylaw: string;
  deptBooklet: string;

  public chart: any;
  constructor( private router: Router,private route: ActivatedRoute,private deptservice:DepartmentService ) {
    this.deptid=this.router.getCurrentNavigation().extras.state.id;
    this.deptname=this.router.getCurrentNavigation().extras.state.name;
    this.deptcode=this.router.getCurrentNavigation().extras.state.code;

   }
   openProfile(id:number){
    this.router.navigate(['/viewotherprofiles'],{ state:{ id:id } });

   }
   openCourse(id:number){
    this.router.navigate(['/course'],{ state:{ id:id } });
   }
  ngOnInit(): void {
    this.deptservice.getDepartmentByID(this?.deptid).subscribe({
      next:(data)=>{
        console.log(data);
        this.department=data;
        this.booklet = this.department?.bookletURL;
        this.bylaw = this.department?.bylawURL;
        this.deptBylaw = String(this.booklet).substring(
          String(this.department.bylawURL).lastIndexOf('/') + 1
        );

        this.deptBooklet = String(this.bylaw).substring(
          String(this.department.bookletURL).lastIndexOf('/') + 1
        );

      }
    })
    //legations
    this.deptservice.getNumOFLeagations(this?.deptcode).subscribe({
      next:(data)=>{
        console.log("legation",data);
        this.seperateLegation(data);
        this.createlegationChart()
        // this.department=data;

      }
    })
    //legations
    this.deptservice.getNumOFSecondments(this?.deptcode).subscribe({
      next:(data)=>{
        console.log("secondment",data);
        this.seperateSecondment(data);
        this.createSecondmentChart();
        // this.department=data;

      }
    })
  }
  createlegationChart() {
    // set the dataset array containing data1 and data2 if data1 and data2 are not null
    let arr = [];

      arr = [
        {
          label: this.Llabel2,
          data: this.legationNumbers,
          backgroundColor: '#1d2c28',
        },
      ];

    this.chart = new Chart('bar', {
      type: 'bar', //this denotes tha type of chart

      data: {
        // values on X-Axis
        labels: this.legationYears,
        datasets: arr,
      },
    });
  }
  createSecondmentChart() {
    // set the dataset array containing data1 and data2 if data1 and data2 are not null
    let arr = [];

      arr = [
        {
          label: this.Slabel1,
          data: this.secondmentNumbers,
          backgroundColor: '#1d2c28',
        },
      ];

    this.chart = new Chart('bar2', {
      type: 'bar', //this denotes tha type of chart

      data: {
        // values on X-Axis
        labels: this.secondmentYears,
        datasets: arr,
      },
    });
  }
  // seperateSecondment(data:[{Year:string,NumberofSecondments:number}]){

  seperateSecondment(data:any){

    for (let index = 0; index < data.length; index++) {
      const element = data[index];
      console.log(element);

      this.secondmentYears.push(element.Year);
      this.secondmentNumbers.push(element.NumberofSecondments)
    }
    console.log("secondmentYears",this.secondmentYears);
    console.log("secondmentNumbers",this.secondmentNumbers);

  }

  seperateLegation(data:any){

    for (let index = 0; index < data.length; index++) {
      const element = data[index];
      this.legationYears.push(element.Year);
      this.legationNumbers.push(element.NumberofLegations)
    }
  }




}
