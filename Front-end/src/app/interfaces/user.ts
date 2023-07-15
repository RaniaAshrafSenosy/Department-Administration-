
export interface User {
  full_name:string;
  user_name:string;
  phone_number:string;
  relative_name:string;
  relative_number:string;
  main_email:string;
  additional_email:string;
  profile_links:string[];
  role:string;
  title:string;
  office_location:string;
  is_active:boolean;
  dept_code:string;
  start_date:string;
  end_date:string;
  imageUrl:string
  time_range:[{start:string,end:string,day_time:string}]
}
