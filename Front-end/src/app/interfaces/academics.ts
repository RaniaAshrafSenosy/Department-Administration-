export interface Department {
  professor_names: {
    user_id: number;
    full_name: string;
    image:string;
  }[];
  ta_names: {
    user_id: number;
    full_name: string;
    image:string;

  }[];
  secretary_names: {
    user_id: number;
    full_name:string;
    image:string;
  }[];
}

export interface ResponseData {
  [key: string]: Department;
}
