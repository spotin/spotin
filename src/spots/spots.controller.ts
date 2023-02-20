import { Body, Controller, Get, Post } from '@nestjs/common';
import { SpotsService } from './spots.service';
import { CreateSpot } from './types/create-spot-type';

@Controller('api/spots')
export class SpotsController {
  constructor(private readonly spotService: SpotsService) {}

  @Get()
  async getSpotApi() {
    const slideshows = await this.spotService.getSpots();

    return slideshows;
  }

  @Post()
  async createSpotApi(@Body() createSpot: CreateSpot) {
    const newSlideshow = await this.spotService.createSpot(createSpot);

    return newSlideshow;
  }
}
