import {
  Body,
  Controller,
  Delete,
  Get,
  NotFoundException,
  Param,
  Patch,
  Post,
} from '@nestjs/common';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';
import { SpotsService } from './spots.service';

@Controller('api/spots')
export class SpotsController {
  constructor(private readonly spotsService: SpotsService) {}

  @Get()
  async getSpotsApi() {
    const Spots = await this.spotsService.getSpots();

    return Spots;
  }

  @Get(':uuid')
  async getSpotApi(@Param('uuid') uuid: string) {
    const Spot = await this.spotsService.getSpot(uuid);

    if (!Spot) {
      throw new NotFoundException();
    }

    return Spot;
  }

  @Post()
  async createSpotApi(@Body() createSpot: CreateSpotDto) {
    const newSpot = await this.spotsService.createSpot(createSpot);

    return newSpot;
  }

  @Patch(':id')
  async updateSpotApi(
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    try {
      const updatedSpot = await this.spotsService.updateSpot(id, updateSpot);

      return updatedSpot;
    } catch (error) {
      throw new NotFoundException();
    }
  }

  @Delete(':id')
  async deleteSpotApi(@Param('id') id: string) {
    const deletedSpots = await this.spotsService.deleteSpot(id);
    if (deletedSpots.length === 0) {
      throw new NotFoundException();
    }
    return deletedSpots;
  }
}
