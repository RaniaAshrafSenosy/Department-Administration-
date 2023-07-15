//

export interface department {
  id: number,
  dept_code: string,
  dept_name: string,
  head: string,
  head_id:number,
  desc:string,
  bylaw:File,
  booklet:File,
  bylawURL:File,
  bookletURL:File,
  professor_names: {
    User_id: number;
    User_name: string;
  }[];
  ta_names: {
    User_id: number;
    User_name: string;

  }[];
  secretary_names: {
    User_id: number;
    User_name:string;
  }[];
  course_names: {
    course_id: number;
    course_name:string;
  }[];
}
