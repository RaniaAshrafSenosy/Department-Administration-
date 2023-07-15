export interface programs {
  id: number,
  dept_code: string,
  program_name: string,
  program_desc: string,
  program_head:number,
  bylaw:File,
  booklet:File,
  bylawURL:File,
  bookletURL:File,
  course_names: {
    course_id: number;
    course_name:string;
  }[];
}
