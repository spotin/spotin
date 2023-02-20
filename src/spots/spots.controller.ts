import {
  Body,
  Controller,
  Get,
  NotFoundException,
  Param,
  Patch,
  Post,
} from '@nestjs/common';
import { SpotsService } from './spots.service';
import { CreateSpot } from './types/create-spot-type';
import { UpdateSpot } from './types/update-spot-type';

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

  @Patch(':id')
  async updateSlideshowApi(
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpot,
  ) {
    // try {
    const updatedSlideshow = await this.spotService.updateSpot(id, updateSpot);

    return updatedSlideshow;
    // } catch (error) {
    //   throw new NotFoundException();
    // }
  }
}
