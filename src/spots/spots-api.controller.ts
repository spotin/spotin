import {
  Body,
  Controller,
  Delete,
  Get,
  HttpCode,
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
  ApiParam,
  ApiNotFoundResponse,
  ApiNoContentResponse,
} from '@nestjs/swagger';
import { SpotDto } from './dtos/spot.dto';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';
import { SpotsService } from './spots.service';
import { Response } from 'express';
import { MissingOrIncorrectFieldsResponse } from '../common/openapi/responses';

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

  @Get(':id')
  @ApiOperation({
    summary: 'Get the specified spot',
    description: 'Get the specified spot.',
    operationId: 'getSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Spot has been successfully retrieved.',
    type: SpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotApi(@Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    if (!spot) {
      throw new NotFoundException();
    }

    return spot;
  }

  @Get(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirection success.',
    type: SpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(@Res() res: Response, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    if (!spot?.configured) {
      res.redirect(`/spots/${id}/edit`);
    }

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @Post()
  @ApiOperation({
    summary: 'Create a new spot',
    description: 'Create a new spot.',
    operationId: 'createSpotApi',
  })
  @ApiBody({
    description: "The spot's details.",
    type: CreateSpotDto,
  })
  @ApiCreatedResponse({
    description: 'Spot has been successfully created.',
    type: SpotDto,
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async createSpotApi(@Body() createSpot: CreateSpotDto) {
    const newSpot = await this.spotsService.createSpot(createSpot);

    return newSpot;
  }

  @Patch(':id')
  @ApiOperation({
    summary: 'Update the specified spot',
    description: 'Update the specified spot.',
    operationId: 'updateSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiBody({
    description: "The spot's details.",
    type: UpdateSpotDto,
  })
  @ApiOkResponse({
    description: 'Spot has been successfully updated.',
    type: SpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
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
  @HttpCode(204)
  @ApiOperation({
    summary: 'Delete the specified spot',
    description: 'Delete the specified spot.',
    operationId: 'deleteSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiNoContentResponse({
    description: 'Spot has been successfully deleted.',
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async deleteSpotApi(@Param('id') id: string) {
    try {
      await this.spotsService.deleteSpot(id);
    } catch {
      throw new NotFoundException();
    }
  }
}
