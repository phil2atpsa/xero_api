import { Component, OnInit, ViewChild } from '@angular/core';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {CollectionsService} from '../../../services/collections.service';
import {MatDialog, MatDialogConfig} from "@angular/material";
import swal from 'sweetalert2';
import {CollectionsEditFormComponent} from "./collections-edit-form/collections-edit-form.component";

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
    'actions',
    'edit_action'
  ];

  loading = false;
  matDialogConfig: MatDialogConfig = new MatDialogConfig();


  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private collectionsService: CollectionsService, private dialog: MatDialog) {
    this.matDialogConfig.disableClose = true;
    this.matDialogConfig.autoFocus = true;
    this.matDialogConfig.width = '55%';
  }

  ngOnInit() {
    this.refresh();
    this.autorefresh();
  }

  autorefresh(){
    setInterval(
      () => {this.refresh()},
      300000
    );
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

  edit(id: number){
    this.collectionsService.getSingle(id)
      .then(res=> {
        localStorage.setItem('collection', JSON.stringify(res));
        this.dialog.open(CollectionsEditFormComponent)
          .afterClosed()
          .subscribe(res => {
            this.refresh();
          });
      });
  }
}


