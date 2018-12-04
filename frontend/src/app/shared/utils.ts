/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
export class Utils{
    
     static currentDate(): string {
        const currentDate = new Date();
        return currentDate.toISOString().substring(0,10);
    }
    
}

