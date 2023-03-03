import {
  Body,
  Controller,
  Delete,
  Get,
  NotFoundException,
  Param,
  Patch,
  Post,
  Res,
} from '@nestjs/common';
import {
  ApiBadRequestResponse,
  ApiBody,
  ApiCreatedResponse,
  ApiOperation,
  ApiOkResponse,
  ApiTags,
} from '@nestjs/swagger';
import { SpotDto } from './dtos/spot.dto';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';
import { SpotsService } from './spots.service';
import { Response } from 'express';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @Get()
  @ApiOperation({
    summary: 'Get the spots',
    description: 'Get the spots.',
    operationId: 'getSpotsApi',
  })
  @ApiOkResponse({
    description: 'Spots have been successfully retrieved.',
    type: [SpotDto],
  })
  async getSpotsApi() {
    const Spots = await this.spotsService.getSpots();

    return Spots;
  }

  // @Get(':uuid')
  // @ApiOperation({
  //   summary: 'Create a new user',
  //   description: 'Create a new user.',
  //   operationId: 'create',
  // })
  // @ApiBody({
  //   description: "The user's details.",
  //   type: () => CreateUserDto,
  // })
  // @ApiCreatedResponse({
  //   description: 'User has been successfully created.',
  //   type: () => UserDto,
  // })
  // @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async getSpotApi(@Param('uuid') uuid: string) {
    const Spot = await this.spotsService.getSpot(uuid);

    if (!Spot) {
      throw new NotFoundException();
    }

    return Spot;
  }

  @Get(':uuid/redirect')
  async getSpotRedirection(@Res() res: Response, @Param('uuid') uuid: string) {
    const spot = await this.spotsService.getSpot(uuid);

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @Post()
  async createSpotApi(@Body() createSpot: CreateSpotDto) {
    const newSpot = await this.spotsService.createSpot(createSpot);

    return newSpot;
  }

  @Patch(':uuid')
  async updateSpotApi(
    @Param('uuid') uuid: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    try {
      const updatedSpot = await this.spotsService.updateSpot(uuid, updateSpot);

      return updatedSpot;
    } catch (error) {
      throw new NotFoundException();
    }
  }

  @Delete(':uuid')
  async deleteSpotApi(@Param('uuid') uuid: string) {
    try {
      await this.spotsService.deleteSpot(uuid);
    } catch {
      throw new NotFoundException();
    }
  }
}
