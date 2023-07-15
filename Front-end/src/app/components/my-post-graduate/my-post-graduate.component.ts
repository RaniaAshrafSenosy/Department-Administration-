import { Component, OnInit } from '@angular/core';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-my-post-graduate',
  templateUrl: './my-post-graduate.component.html',
  styleUrls: ['./my-post-graduate.component.css']
})
export class MyPostGraduateComponent implements OnInit {

  array=[];
  constructor(private profile:ProfileService) { }
  postgrad:any[];
  ngOnInit(): void {
    this.profile.getMyPostgraduates().subscribe({
      next:(data)=>{
        if(data['message']=='success')
        this.postgrad=data['all postgrads'];
        console.log(this.postgrad);



      }
    })

  }


}
