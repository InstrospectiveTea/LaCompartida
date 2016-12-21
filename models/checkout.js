var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var moment = require('moment');

var CheckOutSchema = new Schema({
  book_id: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Book',
    required: true
  },
  person: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Person',
    required: true
  },
  from: {
    type: Date,
    default: moment().toDate()
  },
  to: {
    type: Date,
    default: moment().toDate()
  }
}, {
  timestamps: true
});

module.exports = mongoose.model('CheckOut', CheckOutSchema);
