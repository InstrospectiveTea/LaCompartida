var express = require('express');
var router = express.Router();
var Checkout = require('../models/checkout');

// /checkouts
router.route('/')

  .get(function (req, res, next) {
    Checkout.find(function (err, checkouts) {
      if(err){
        return next(err);
      }
      res.json(checkouts);
    });
  })

  .post(function(req, res, next){
    var from = moment(req.body.from, 'DD-MM-YYYY');
    if(!/\d\d-\d\d-\d{4}/.test(req.body.from) || !from.isValid()){
      req.body.from = null;
    }
    else{
      req.body.from = from.toDate();
    }

    var to = moment(req.body.to, 'DD-MM-YYYY');
    if(!/\d\d-\d\d-\d{4}/.test(req.body.to) || !to.isValid()){
      req.body.to = null;
    }
    else{
      req.body.to = to.toDate();
    }
    var checkout = new Checkout(req.body);
    checkout.save(function(err) {
      if(err){
        return next(err);
      }
      res.status(200).send({ message: 'Checkout created.', data: checkout });
    });
  });

router.route('/:checkout_id')
  .get(function (req, res, next) {
    Checkout.findById(req.params.checkout_id, function (err, checkout) {
      if(err){
        return next(err);
      }
      if(!checkout){
        return res.json({});
      }
      res.json(checkout);
    })
  })

  .put(function (req, res, next) {
    Checkout.findById(req.params.checkout_id, function (err, checkout) {
      if (err){
        return next(err);
      }

      if(!checkout){
        return next({
          name: 'MissingError',
          message: 'There\'s no resource with that id'
        });
      }

      checkout.new_attributes(req.body);
      checkout.save(function(err) {
        if(err){
          next(err);
        }
        res.json({ message: 'Checkout updated.', data: checkout });
      })
    })
  })

  .delete(function (req, res, next) {
    Checkout.remove({
      _id: req.params.checkout_id
    }, function (err, checkout) {
      if(err){
        return next(err);
      }
      res.json({ message: 'Checkout deleted.' });
    });
  })

module.exports = router;
