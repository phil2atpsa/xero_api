export interface Store {
   id: number;
   parent_id: number;
   name: string;
   business_id: number;
   shopping_center: string;
   lat: number;
   lng: number;
   address: string;
   area_code: number;
   province: string;
   shop_number: string;
   suburb: string;
   city: string;
   tel_number: string;
   avatar: string;
   is_unique: boolean;
   created_at: string;
   updated_at: string;
   operating_times_weekday_from: string;
   operating_times_weekday_to: string;
   operating_times_saturday_from: string;
   operating_times_saturday_to: string;
   operating_times_sunday_from: string;
   operating_times_sunday_to: string;
   operating_times_ph_from: string;
   operating_times_ph_to: string;
   store_representatives_name: string;
   store_representatives_surname: string;
   store_representatives_email: string;
   store_representatives_cell_no: string;

}
