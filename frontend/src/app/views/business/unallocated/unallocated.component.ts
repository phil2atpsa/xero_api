import { Component, OnInit, ViewChild } from '@angular/core';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {UnallocatedService} from '../../../services/unallocated.service';
import swal from 'sweetalert2';

@Component({
  selector: 'app-unallocated',
  templateUrl: './unallocated.component.html',
  styleUrls: ['./unallocated.component.scss']
})
export class UnallocatedComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey = '';

  displayedColumns: string[] = [
    'policy_number',
    'reference',
    'bank',
    'method',
    'amount',
    'actions'
  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private service : UnallocatedService) { }

  ngOnInit() {
    this.refresh();
    setInterval(
      () => {
        this.refresh();
      }, 180000
    );
  }

  refresh() {
    this.list = null;

    this.service.getPayments()
      .subscribe(
      list => {
        const array = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        this.list = new MatTableDataSource(array);
        this.list.sort = this.sort;
        this.list.paginator = this.paginator;
      }
    );
  }
  onSearchClear() {
    this.searchKey = '';
    this.applyFilter();
  }
  applyFilter() {
    this.list.filter = this.searchKey.trim().toLowerCase();
  }

  allocate_payment(paymentID : number){
    swal({
      title: 'Allocating!',
      html: '<b>Please wait while allocating...</b>',
      onOpen: () => {
        swal.showLoading()

        this.service.allocate_payment(paymentID).then(
          res => {
            swal.getContent().textContent = res.message;
            swal.hideLoading();
            if(res.success) {
                setTimeout(()=>{
                    this.refresh();
                    }, 3000)
            }

          }
        );

      },
      onClose: () => {}
    }).then( (result) => {
      // if(result.dismiss = swal.DismissReason.close){}
    })
  }

}
