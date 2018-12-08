import { Component, OnInit, ViewChild } from '@angular/core';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {CollectionsService} from '../../../services/collections.service';
import swal from 'sweetalert2';

@Component({
  selector: 'app-collections',
  templateUrl: './collections.component.html',
  styleUrls: ['./collections.component.scss']
})
export class CollectionsComponent implements OnInit {

  list: MatTableDataSource<any>;
  searchKey = '';

  displayedColumns: string[] = [
    'tid',
    'reference',
    'contact',
    'mobile',
    'policy_number',
    'collection_amount',
    'actions'
  ];

  loading = false;


  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private collectionsService: CollectionsService) { }

  ngOnInit() {
    this.refresh();
    this.autorefresh();
  }

  autorefresh(){
     const $this = this;
    setTimeout(function(){
       $this.refresh();
    }, 300000)
  }

  refresh() {
    this.list = null;

    this.collectionsService.getCollections().subscribe(
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

  sync(invoiceID:number) {
    swal({
      title: 'Syncing!',
      html: '<b>Please wait while syncing...</b>',
      onOpen: () => {
        swal.showLoading()
        const d = swal;
        this.collectionsService.sync_collection(invoiceID).then(
          res => {
            swal.getContent().textContent = res.message;
            swal.hideLoading();
            setTimeout(()=>{
              swal.close();
              this.refresh();
            }, 3000)

          },  rej => {
            swal.getContent().textContent = rej.message;
            swal.hideLoading();
            setTimeout(()=>{
              swal.close();

            }, 3000)
          }
        );

      },
      onClose: () => {}
    }).then( (result) => {
      // if(result.dismiss = swal.DismissReason.close){}
    })

  }
}


