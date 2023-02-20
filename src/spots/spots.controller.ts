import {
  Body,
  Controller,
  Get,
  NotFoundException,
  Param,
  Post,
} from '@nestjs/common';
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

  @Get(':uuid')
  async getSlideshowApi(@Param('uuid') uuid: string) {
    const slideshow = await this.spotService.getSpot(uuid);

    if (!slideshow) {
      throw new NotFoundException();
    }

    return slideshow;
  }

  @Post()
  async createSpotApi(@Body() createSpot: CreateSpot) {
    const newSlideshow = await this.spotService.createSpot(createSpot);

    return newSlideshow;
  }
}
