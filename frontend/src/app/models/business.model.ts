import {Deserializable} from './deserializable.model';

export class Business implements Deserializable {

  public id: number;
  public  user_id: number;
  public name: string;
  public business_type: string;
  public avatar: string;
  public created_at: string;
  public updated_at: string;



  deserialize(input: any): this {
    Object.assign(this, input);
    return this;
  }
}
