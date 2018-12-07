import { Component, OnInit, ViewChild } from '@angular/core';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {CollectionsService} from '../../../services/collections.service';

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
  ];



  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;

  constructor(private collectionsService: CollectionsService) { }

  ngOnInit() {
    this.refresh();
  }

  refresh() {
    this.list = new MatTableDataSource([]);
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

}


