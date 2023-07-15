import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { programs } from 'src/app/interfaces/programs';
import { ProgramService } from 'src/app/services/program';

@Component({
  selector: 'app-program',
  templateUrl: './program.component.html',
  styleUrls: ['./program.component.css']
})
export class ProgramComponent implements OnInit {
  progid:number;
  programs:programs
  constructor(private router: Router,private progservice:ProgramService) {
    this.progid=this.router.getCurrentNavigation().extras.state.id;
   }

  ngOnInit(): void {
    this.progservice.getProgramByID(this?.progid).subscribe({
      next:(data)=>{
        console.log(data);
        this.programs=data.department;
      }
    })
  }
  openCourse(id:number){
    this.router.navigate(['/course'],{ state:{ id:id } });
  }

}
